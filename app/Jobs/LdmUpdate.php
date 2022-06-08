<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use \COM;
class LdmUpdate extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //Ref
        //https://answers.sap.com/questions/1448088/using-xml-to-update-objects-in-diapi.html
        //https://biuan.com/ProductTrees/

        $vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        $vCmp->DbServerType = "10";
        $vCmp->server = env('SAP_server');
        $vCmp->LicenseServer = env('SAP_LicenseServer');
        $vCmp->CompanyDB = env('SAP_CompanyDB');
        $vCmp->username = env('SAP_username');
        $vCmp->password = env('SAP_password');
        $vCmp->DbUserName = env('SAP_DbUserName');
        $vCmp->DbPassword = env('SAP_DbPassword');
        $vCmp->UseTrusted = false;
        //la siguiente linea permite leer XML como string y no como archivo en "Browser->ReadXml"
        $vCmp->XMLAsString = true; //The default value is False - XML as files.

        //$vCmp->language = "6";
        $vCmp->Connect; //conectar a Sociedad SAP

        //Obtener XML de un LDM 
        $vCmp->XmlExportType = '3'; //BoXmlExportTypes.xet_ExportImportMode; /solo los campos modificables
        $vItem = $vCmp->GetBusinessObject("66"); //ProductTrees table: OITT.
        $vItem->GetByKey("20185"); //LDM Docentry
        //$vItem->SaveXML($pathh); //Guardar en archivo
        $xmlString = $vItem->GetAsXML(); //Guardar XML en buffer
        //retiramos Utf16 del XML obtenido
        $xmlString = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlString);
        //Leemos XML(string) y creamos Object SimpleXML 
        $oXML = simplexml_load_string($xmlString);
        //$library = simplexml_load_file($pathh); //Crear Object SimpleXML de un archivo

        //Modificar los campos en el XML (de un articulo de la LDM)
        $item = $oXML->xpath('/BOM/BO/ProductTrees_Lines/row[ItemCode="20189"]');

        if (count($item) >= 1) {
            foreach ($item as $i) {
                $i->Quantity = '1';
            }
        } else {

            throw new \Exception("Error Processing ldmUpdate", 1);
        }

        //Cargar el XML en la LDM y actualizar en SAP
        //$library->asXML($pathh); //Elaborar y Escribir el XML

        //To use ReadXML method, set the XmlExportType to xet_ExportImportMode (3).
        $vItem->Browser->ReadXml($oXML->asXML(), 0);
        // $vItem->UpdateFromXML($pathh);
        $resultadoOperacion = $vItem->Update;
        if ($resultadoOperacion <> 0) {
            throw new \Exception( $vCmp->GetLastErrorDescription(), 1);
        } 
        $vCmp->Disconnect;
        $vCmp = null;
        $vItem = null;
        $xmlString = null;
        $oXML = null;
        $item = null;
        $resultadoOperacion = null;

           } catch (\Exception $e) {
               dd($e->getMessage());
           }
        
    }
}
