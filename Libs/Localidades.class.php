<?php
/**
 * Localidades
 *
 * Lista Países, Estados e Cidades
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Localidades
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009 
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 * @since      2007-04-02
 */

/**
 * @see Db
 */
require_once dirname(__FILE__) . '/Db.class.php';

/**
 * @category   Classes
 * @subpackage Localidades
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009 
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Localidades
{
    /**
     * Retorna as cidades de um país
     *
     * @static
     * @access  public
     * @param   integer $estadoID
     * @param   integer $paisID
     * @return  array
     */
    public static function cidades($estadoID = null, $paisID = null)
    {
        $arrRetorno = array();
        $params     = array(
            'intPais'   => $paisID,
            'intEstado' => $estadoID
        );
        $strXml     = dirname(__FILE__) . '/Localidades.xml';
        
        $arrCidades = Db::call('listarCidades', $params, $strXml);

        foreach ($arrCidades as $arrCidade) {
            $arrRetorno[ $arrCidade['cod'] ] = stripslashes($arrCidade['nome']);
        }

        return $arrRetorno;
    }

    /**
     * Retorna os estados
     *
     * @static
     * @access  public
     * @param   integer $paisID
     * @return  array
     */
    public static function estados($paisID = null)
    {
        $arrRetorno = array();
        $arrParams  = array(
            'intPais' => $paisID
        );
        $strXml     = dirname(__FILE__) . '/Localidades.xml';

        $arrEstados = Db::call('listarEstados', $arrParams, $strXml);

        foreach ($arrEstados as $arrEstado) {
            $arrRetorno[ $arrEstado['Cod'] ] = stripslashes($arrEstado['Nm']);
        }

        return $arrRetorno;
    }

    /**
     * Retorna os países
     *
     * @static
     * @access  public
     * @return  array
     */
    public static function paises()
    {
        $arrRetorno = array();
        $strXml     = dirname(__FILE__) . '/Localidades.xml';
        
        $arrPaises  = Db::call('listarPaises', null, $strXml);

        foreach ($arrPaises as $arrPais) {
            $arrRetorno[ $arrPais['Cod'] ] = array(
                'Cod'   => $arrPais['Cod'],
                'Nm'    => $arrPais['Nm'],
                'sigla' => $arrPais['sigla']
            );
        }

        return $arrRetorno;
    }
}
