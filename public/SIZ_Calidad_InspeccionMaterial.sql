CREATE PROCEDURE [dbo].[SIZ_Calidad_InspeccionMaterial]
    @NumeroEntrada int = 0
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Consultar solo las tablas de la base de datos siz
    -- Sumar todas las inspecciones parciales por material
    SELECT 
        INC.INC_docNum AS NOTA_ENTRADA,
        INC.INC_codMaterial AS CODIGO_ARTICULO,
        INC.INC_nomMaterial AS MATERIAL,
        INC.INC_unidadMedida AS UDM,
        INC.INC_cantRecibida AS CANTIDAD,
        INC.INC_esPiel,
        -- Datos de inspección sumados de todas las inspecciones parciales
        ISNULL(SUM(INC.INC_cantAceptada), 0) AS CAN_INSPECCIONADA,
        ISNULL(SUM(INC.INC_cantRechazada), 0) AS CAN_RECHAZADA,
        (INC.INC_cantRecibida - ISNULL(SUM(INC.INC_cantAceptada), 0) - ISNULL(SUM(INC.INC_cantRechazada), 0)) AS POR_REVISAR,
        -- ID de la última inspección (para referencia)
        MAX(INC.INC_id) AS ID_INSPECCION
    FROM Siz_Incoming INC
    WHERE INC.INC_docNum = @NumeroEntrada 
        AND INC.INC_borrado = 'N'
    GROUP BY 
        INC.INC_docNum,
        INC.INC_codMaterial,
        INC.INC_nomMaterial,
        INC.INC_unidadMedida,
        INC.INC_cantRecibida,
        INC.INC_esPiel
    ORDER BY INC.INC_nomMaterial;
END 