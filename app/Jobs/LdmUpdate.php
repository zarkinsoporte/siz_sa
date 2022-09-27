<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use \COM;
use App\LOG;
use DB;
class LdmUpdate extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $codigo_origen;
    protected $codigo;
    protected $codigo_cambio;
    protected $cantidad;
    protected $delete_option;
    protected $cambio_option;
    public $user_nomina;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($codigo, $codigo_origen, $cantidad, $delete_option, $cambio_option, $codigo_cambio, $user_nomina)
    {
        $this->codigo = $codigo;
        $this->codigo_cambio = $codigo_cambio;
        $this->codigo_origen = $codigo_origen;
        $this->cantidad = $cantidad;
        $this->delete_option = $delete_option;
        $this->cambio_option = $cambio_option;
        $this->user_nomina = $user_nomina;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    
     public function handle()
    {
         //dd($this->codigo_origen);
        try{
            //Ref
        //https://answers.sap.com/questions/1448088/using-xml-to-update-objects-in-diapi.html
        //https://biuan.com/ProductTrees/

        $vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        $vCmp->DbServerType = "10";
        $vCmp->server = "".env('SAP_server');
        $vCmp->LicenseServer = "".env('SAP_LicenseServer');
        $vCmp->CompanyDB = "".env('SAP_CompanyDB');
        $vCmp->username = "".env('SAP_username');
        $vCmp->password = "".env('SAP_password');
        $vCmp->DbUserName = "".env('SAP_DbUserName');
        $vCmp->DbPassword = "".env('SAP_DbPassword');
        $vCmp->UseTrusted = false;
        //la siguiente linea permite leer XML como string y no como archivo en "Browser->ReadXml"
        $vCmp->XMLAsString = true; //The default value is False - XML as files.

        //$vCmp->language = "6";
        $vCmp->Connect; //conectar a Sociedad SAP

        //Obtener XML de un LDM 
        $vCmp->XmlExportType = "3"; //BoXmlExportTypes.xet_ExportImportMode; /solo los campos modificables
        $vItem = $vCmp->GetBusinessObject("66"); //ProductTrees table: OITT.
        $vItem->GetByKey($this->codigo_origen.""); //LDM Docentry
        //$pathh = public_path('assets/xml/sap/ldm/20185.xml');
        //$vItem->SaveXML($pathh); //Guardar en archivo
        $xmlString = $vItem->GetAsXML(); //Guardar XML en buffer
        //retiramos Utf16 del XML obtenido
        $xmlString = utf8_encode(preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlString));
        //Leemos XML(string) y creamos Object SimpleXML 
        $oXML = simplexml_load_string($xmlString);
        //$library = simplexml_load_file($pathh); //Crear Object SimpleXML de un archivo

        //Modificar los campos en el XML (de un articulo de la LDM)
        $item = $oXML->xpath('/BOM/BO/ProductTrees_Lines/row[ItemCode="'.$this->codigo.'"]');
        // clock($item);
        if ($this->cambio_option) {
            //$items_root = $oXML->xpath('/BOM/BO/ProductTrees_Lines');
            if (count($item) >= 1 && !empty($item)) {    
                $nombreItem = DB::table('OITM')->where('ItemCode', $this->codigo_cambio)->value('ItemName');            
                foreach ($item as $i) {
                       /*  $pelicula = $items_root->addChild('pelicula');
                        $pelicula->addChild('ItemCode', $this->codigo);
                         $pelicula->addChild('Warehouse', 'APG-ST');*/
                    
           /*          <ItemCode>20189</ItemCode>
<Quantity>1.300000</Quantity>
<Warehouse>APG-ST</Warehouse>
<Price>119.700000</Price>
<Currency>MXP</Currency>
<IssueMethod>im_Backflush</IssueMethod>
<ParentItem>20185</ParentItem>
<PriceList>1</PriceList>
<ItemType>pit_Item</ItemType>
<AdditionalQuantity>0.000000</AdditionalQuantity>
<ChildNum>0</ChildNum>
<ItemName>HE, 17E</ItemName>
<U_Estacion>145</U_Estacion> */

                        $i->Quantity =  (float) filter_var($this->cantidad, 
                        FILTER_SANITIZE_NUMBER_FLOAT, 
                        FILTER_FLAG_ALLOW_FRACTION);
                        $i->ItemName = $nombreItem ."";
                        $i->Price = "";
                        $i->ItemCode =  $this->codigo_cambio . "";
                }
            } else {
                throw new \Exception("Error Processing ldmUpdate, item no encontrado", 1);
            }
        }else if ( $this->delete_option && !empty($item)) {
            unset($item[0][0]);
        } else{
            if (count($item) >= 1 && !empty($item)) {
                foreach ($item as $i) {
                    $i->Quantity = (float) filter_var($this->cantidad, 
                    FILTER_SANITIZE_NUMBER_FLOAT, 
                    FILTER_FLAG_ALLOW_FRACTION);
                   // clock($this->cantidad);
                }
            } else {
              throw new \Exception("Error Processing ldmUpdate, item no encontrado", 1);
            }
        }

        //Cargar el XML en la LDM y actualizar en SAP
        //$library->asXML($pathh); //Elaborar y Escribir el XML
        //To use ReadXML method, set the XmlExportType to xet_ExportImportMode (3).
        $vItem->Browser->ReadXml($oXML->asXML(), 0);
        // $vItem->UpdateFromXML($pathh);
        $resultadoOperacion = "".$vItem->Update;

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
            throw new \Exception("Error Processing ldmUpdate ". $e , 1);
            
        }
        
    }
}
