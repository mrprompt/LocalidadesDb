<?php
require_once dirname(__FILE__) . '/../../Autoloader.php';

$strSaida = null;
$strParam = isset($_GET['param']) ? $_GET['param'] : 'paises';

switch($strParam)
{
    case 'cidades':
        $paisId   = isset($_GET['pais'])   ? $_GET['pais'] : null;
        $estadoId = isset($_GET['estado']) ? $_GET['estado'] : null;
        
        $arrCidades = Localidades::cidades($estadoId, $paisId);

        foreach ($arrCidades as $cidadeID => $cidadeNome )
        {
            $strSaida .= $cidadeID .';'. $cidadeNome .',';
        }
        break;

    case 'estados':
        $arrEstados  = Localidades::estados($_GET['pais']);

        foreach ($arrEstados as $estadoID => $estadoNome)
        {
            $strSaida .= $estadoID . ';' . $estadoNome . ',';
        }
        break;

    default:
        $arrPaises   = Localidades::paises();

        foreach ($arrPaises as $paises)
        {
            $strSaida .= $paises['Cod'] .';'. $paises['Nm'] .',';
        }
        break;
}

echo substr($strSaida, 0, -1);