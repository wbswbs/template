<?php
/**
 * Container for the class template
 *
 *  A very simple template engine
 *
 * @package    wbsFramework
 * @subpackage template
 */

/*******************************************************************************
 *   N A M E S P A C E
 ******************************************************************************/

namespace wbswbs;

/**
 * @class template
 *
 * Die Template Klasse bekommt bei der Initierung den Dateinamen des Templates dazu
 * Mit setContent($content) werden die Variablen übergeben als key value
 * Der geparste Code kann mit getOutput() abgeholt werden.
 *
 *  Description:
 *
 * Calling the Template with the Filename in the Constructor:
 *
 *    $tpl = new \de\blessen\wbsfw\template\template('path/to/filename.tpl'));
 *
 *    File Ending .tpl is not a must but common
 *
 *    Template-Placeholder are formated like {{<key>}}
 *
 *   To replace Placeholder, use an array with key=>value
 *
 *   Submit the Replacements with $tpl->setContent(<array>);
 *
 *   Get the Output with $tpl->getOutput();
 *
 * Beispielaufruf
 *
 *    $seite = new \wbswbs\Template(ROOT_PATH. 'templates/body_user_subject.tpl');
 *    $parameter['navigation']= 'jhgfhjgfjhgfjhfg';
 *    $parameter['wert1']        = 'Wert1';
 *    $seite->setContent($parameter);
 *    echo $seite->getOutput();
 *
 * @author wbs
 * @version
 *
 */
class Template
{

    /**
     * @string
     */
    const TEMPLATE_TYPE_FILE = 'file';
    /**
     * @string
     */
    const TEMPLATE_TYPE_UNI_FILE = 'uni_file';
    /**
     * @string
     */
    const TEMPLATE_TYPE_CONTENT = 'content';
    /**
     * @var string
     */
    protected $filename;
    /**
     * @var string
     */
    protected $filecontent;
    /**
     * @var string[]
     */
    protected $content;

    /**
     * Konstruktor nimmt entweder den Dateinamen , oder
     * den Inhalt des Templates mit new \de\blessen\wbsfw\template\template('hallo {{wbs}name}','content'
     *
     * $type = 'uni_file' -> Eine Datei für alle Sprachen
     *
     * @param string $filename Dateiname oder Inhalt
     * @param string $type file[default] oder content
     *
     * @throws Exception
     */
    public function __construct($filename, $type = 'file')
    {
        $this->setTemplate(
            $filename,
            $type
        );
        $this->content = [];
    }

    /**
     * Konstruktor nimmt entweder den Dateinamen , oder
     * den Inhalt des Templates mit new \de\blessen\wbsfw\template\template('hallo {{wbs}name}','content'
     *
     * $type = 'uni_file' -> Eine Datei für alle Sprachen
     *
     * @param string $filename Dateiname oder Inhalt
     * @param string $type file[default] oder content
     *
     * @throws Exception
     */
    public function setTemplate($filename, $type = 'file')
    {
        switch ($type) {
            case self::TEMPLATE_TYPE_FILE:
                $this->filename = $filename;
                if ($filename) {
                    $this->readFile();
                }
                break;
            case self::TEMPLATE_TYPE_UNI_FILE:
                $this->filename = $filename;
                if ($filename) {
                    $this->readFile();
                }
                break;
            case self::TEMPLATE_TYPE_CONTENT:
                $this->filecontent = $filename;
                break;
            default:
                throw new RuntimeException('Invalid Template Type: ' . $type);
        }
    }

    /**
     *
     */
    public function reset()
    {
        $this->content = [];
    }

    /**
     * Parameter als Array übergeben
     *
     * $content ist ein assoziatives Array mit dem key und dem Value
     *
     * @param array $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Templatedatei einlesen
     */
    function readFile()
    {
//        $templatedatei = '';
        $templatedatei = @fopen(
            $this->filename,
            'rb'
        );
        // Check if File exists WBS 30.5.2002
        if (!$templatedatei) {
            echo '<p class="warning">Templatefile ' . $this->filename . ' ist nicht vorhanden</p>';

            return;
        }

        $template = fread(
            $templatedatei,
            filesize($this->filename)
        );
        fclose($templatedatei);
        $this->filecontent = $template;
    }

    /**
     * Platzhalter  {{wbs}key}  {{key}} und in der Templatedatei ersetzen und Ergebnis zurückgeben
     *
     * @return string
     */
    public function getOutput(): string
    {

        $temp_content = $this->filecontent;
        foreach ((array)$this->content as $key => $value) {
            $value = preg_replace(
                '#\$#',
                '\\\$',
                $value
            );
            $temp_content = preg_replace(
                '/{{' . $key . '}}/',
                $value,
                $temp_content
            );
            $temp_content = preg_replace(
                '/{{wbs}' . $key . '}/',
                $value,
                $temp_content
            );
        }

        return (string)$temp_content;
    }

    /**
     * Show all {{placeholder}} from the Template in a <pre> Fornmatted HTML String,
     * to use it with setContent
     */
    public function getPlaceholderAsCode():string
    {
        $placeholder = $this->getPlaceholder();
        $html = '<pre>' . PHP_EOL;
        //$html .= var_export($placeholder);
        $html .= '$tpl->setContent([' . PHP_EOL;
        foreach($placeholder as $the_key){
            $the_key = str_replace(['{{','}}'],'',$the_key);
            $html .= "'".$the_key."' => '',".PHP_EOL;
        }
        $html = rtrim($html,',');
        $html .= ']);' . PHP_EOL;
        $html .= '</pre>' . PHP_EOL;

        return $html;

    }

    /**
     * Get all {{placeholder}} from the Template
     *
     * Could be extended to {{wbs}placeholder}
     */
    public function getPlaceholder():array
    {
//        $the_body = preg_replace_callback(
//            '#\{\{wbs\}[a-zA-Z0-9_]+\}#',
//            [$this, 'my_replace'],
//            $the_body
//        );

        $matches = [];
        preg_match_all(
            '#\{\{[a-zA-Z0-9_]+\}\}#',
            $this->filecontent,
            $matches
        );

        return $matches[0];
    }

}