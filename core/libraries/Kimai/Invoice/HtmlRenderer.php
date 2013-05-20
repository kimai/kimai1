<?php
/**
 * Class for rendering PHTML invoices templates as PDF.
 *
 * @author Kevin Papst
 */
class Kimai_Invoice_HtmlRenderer extends Kimai_Invoice_AbstractRenderer
{

    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public function render()
    {
        echo $this->getHtml();
    }

    public function getHtml()
    {
        $view = new Kimai_View();
        $view->setScriptPath($this->getTemplateDir().$this->getTemplateFile());

        $data = $this->getModel()->toArray();
//var_dump($data);exit;
        foreach($data as $key => $value) {
            $view->assign($key, $value);
        }

        return $view->render($this->getTemplateFilename());
    }

    protected function getTemplateFilename()
    {
        return 'index.html';
    }


    /**
     * Returns if the file can be rendered.
     *
     * @return bool
     */
    public function canRender()
    {
        return (
            is_dir($this->getTemplateDir() . $this->getTemplateFile()) &&
            is_file($this->getTemplateDir() . $this->getTemplateFile() . DIRECTORY_SEPARATOR . $this->getTemplateFilename())
        );
    }

}
