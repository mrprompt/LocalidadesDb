<?php
/**
 * Db
 *
 * Acesso a Banco de dados utilizando PDO e XML
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Db
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Db
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Db
{
    /**
     * Objeto da conexão
     *
     * @static
     * @access private
     * @var    array
     */
    private static $_objDb = array();

    /**
     * Nome da conexão utilizada
     *
     * @static
     * @access private
     * @var    array
     */
    private static $_connection = array();

    /**
     * Objeto do SimpleXml
     *
     * @static
     * @access private
     * @var    array
     */
    private static $_objXml = array();

    /**
     * Singleton para pegar a conexão do banco
     *
     * @static
     * @access public
     * @return object
     */
    public static function getConnection($strXmlPath = 'default')
    {
        $strCon = self::$_connection[ $strXmlPath ];

        $objNm = array();
        
        if ($strXmlPath !== 'default') {
            $objXml = self::getXml($strXmlPath);
            
            $objNm = $objXml->xpath("//connection[@name='{$strCon}']");

            $strDb = $objNm[0]['db'];

            $strFile = dirname(__FILE__);
            $strDbConn = preg_replace('{__FILE__}i', $strFile, $objNm[0]['db']);
        }

        if (!key_exists($strXmlPath, self::$_objDb)) {
            self::$_objDb[ $strXmlPath ] = null;
        }

        if (!self::$_objDb[ $strXmlPath ] instanceof PDO) {
            try {
                $con  = "{$objNm[0]['type']}:{$strDbConn}";
                $usr  = "{$objNm[0]['user']}";
                $pass = "{$objNm[0]['password']}";
                $parm = array(
                    PDO::ATTR_ERRMODE   => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_CASE      => PDO::CASE_NATURAL
                );

                self::$_objDb[$strXmlPath] = new PDO($con, $usr, $pass, $parm);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return self::$_objDb[$strXmlPath];
    }

    /**
     * Lê o arquivo XML de Metadata
     *
     * @static
     * @access public
     * @param  string $strXmlPath
     * @return object
     */
    private static function getXml($strXmlPath = 'metadata.xml')
    {
        if (!key_exists($strXmlPath, self::$_objXml)) {
            self::$_objXml[ $strXmlPath ] = null;
        }

        if (!self::$_objXml[ $strXmlPath ] instanceof SimpleXMLElement) {
            $strXml = file_get_contents($strXmlPath);

            self::$_objXml[ $strXmlPath ] = new SimpleXMLElement($strXml);
        }

        return self::$_objXml[ $strXmlPath ];
    }

    /**
     * Retorna o SQL selecionado pelo parâmetro $strMetodo
     *
     * @static
     * @access  public
     * @param   string $strMetodo O nome do SQL a ser chamado no metadata
     * @param   string $strXml Localização do arquivo Xml com a query
     * @return  string
     */
    private static function getSql($strMetodo = null, $strXml = null)
    {
        if ($strMetodo !== null && $strXml !== null) {
            $objXml = self::getXml($strXml);
            $objNm  = $objXml->xpath("//query[@name='{$strMetodo}']");

            if (isset($objNm[0])) {
                $conn = $objNm[0]->attributes();

                self::$_connection[ $strXml ] = $conn->connection;

                return $objNm[0]->query;
            } else {
                throw new Exception($strMetodo . ' não encontrado.');
            }
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Call envia uma chamada ao método SQL descrito no arquivo XML de
     * Metadata, repassando os parâmetros passados via array
     *
     * @static
     * @access  public
     * @param   string $metodo A consulta a ser chamada, no node 'name'
     * @param   array  $parametros Um array com os parâmetros da consulta
     * @param   string $xml Caminho do Xml com as consultas
     * @return  array
     */
    public static function call($metodo=null, $parametros=null, $xml=null)
    {
        try {
            if ($xml !== null) {
                $strSql   = self::getSql($metodo, $xml);
            } else {
                $strSql = $metodo;
            }

            $objQuery = self::getConnection($xml)->prepare($strSql);

            $objExec  = $objQuery->execute($parametros);

            if (isset($_GET['booDebug']) && $_GET['booDebug'] == 'true') {
                echo $objExec->debugDumpParams();
            }

            if (preg_match('/(insert|update|delete)/i', $strSql)) {
                return $objExec;
            } else {
                return $objQuery->fetchAll();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retorna o último id inserido na conexão
     *
     * @static
     * @access  public
     * @param   string $metodo A consulta a ser chamada, no node 'name'
     * @param   string $xml Caminho do Xml com as consultas
     * @return  array
     */
    public static function lastInsertId($metodo=null, $xml=null)
    {
        try {
            $strSql   = self::getSql($metodo, $xml);

            return self::getConnection($xml)->lastInsertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function query($strQuery)
    {

    }
}
