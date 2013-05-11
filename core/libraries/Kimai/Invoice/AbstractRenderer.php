<?php
/**
 * Base class for rendering invoices.
 *
 * @author Kevin Papst
 */
abstract class Kimai_Invoice_AbstractRenderer
{
    /**
     * @var string
     */
    private $tempDir = null;
    /**
     * @var string
     */
    private $templateDir = null;
    /**
     * @var string
     */
    private $templateFile = null;
    /**
     * @var Kimai_Invoice_PrintModel
     */
    private $model = null;

    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public abstract function render();

    /**
     * @param string $templateDir
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @param \Kimai_Invoice_PrintModel $model
     */
    public function setModel(Kimai_Invoice_PrintModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Kimai_Invoice_PrintModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $tempDir
     */
    public function setTemporaryDirectory($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return $this->tempDir;
    }

}
