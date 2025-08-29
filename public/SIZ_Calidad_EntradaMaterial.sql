ALTER PROCEDURE [dbo].[SIZ_Calidad_EntradaMaterial]
	-- Add the parameters for the stored procedure here
	
	@NumeroEntrada int = 0
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;
	
Select OPDN.DocNum AS NOTA_ENTRADA
	, Cast(OPDN.DocDueDate as date) AS FECHA_RECEPCION
	, OPDN.CardCode AS CODIGO_PROVEEDOR
	, OPDN.CardName AS NOMBRE_PROVEEDOR
	, OPDN.NumAtCard AS NUM_FACTURA
	, PDN1.ItemCode AS CODIGO_ARTICULO
	, PDN1.Dscription AS MATERIAL
	, PDN1.unitMsr2 AS UDM
	, PDN1.InvQty AS CANTIDAD
	, ISNULL((Select Top (1) OIBT.BatchNum from OIBT Where OIBT.ItemCode = PDN1.ItemCode and OIBT.BaseEntry = OPDN.DocEntry), 'N/A') AS LOTE
	--, 'CANTIDAD INSPECCIONADA' AS CAN_INSPECCIONADA -- LA VAMOS A TOMAR DE OTRA CONSULTA
	--, 'CANTIDAD RECHAZADA' AS CAN_RECHAZADA -- LA VAMOS A TOMAR DE OTRA CONSULTA
	, OITM.ItmsGrpCod AS GRUPO
From OPDN
Inner Join PDN1 on OPDN.DocEntry = PDN1.DocEntry 
Inner Join OITM on PDN1.ItemCode = OITM.ItemCode
Where OPDN.DocNum = @NumeroEntrada
Order By MATERIAL
  
END