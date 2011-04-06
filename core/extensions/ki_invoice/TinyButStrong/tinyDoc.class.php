<?php
/*
 * This file is part of the tinyDoc package.
 * (c) Olivier Loynet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * tinyDoc class.
 *
 * This class extends TinyButStrong class to work with OpenDocument and Word 2007 documents.
 *
 * This class needs : PHP 5.2
 * This class needs : TinyButStrong class
 * This class needs optionally : ZipArchive from PECL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    tinyDoc
 * @subpackage tinyDoc
 * @author     Olivier Loynet <tinydoc@googlegroups.com>
 * @version    $Id$
 */

class tinyDoc extends clsTinyButStrong
{
  const VERSION     = '1.0.3';

  const PICTURE_DPI = 96;
  const INCH_TO_CM  = 2.54;
  const PIXEL_TO_CM = 0.0264583333; // 2.54 / 96

  private
    $zipMethod         = 'shell',
    $zipBin            = 'zip',
    $unzipBin          = 'unzip',

    $sourcePathname    = '',
    $processDir        = '',
    $processBasename   = '',
    $defaultExtension  = 'odt',
    $xmlFilename       = 'content.xml',

    $defaultCharset    = 'UTF-8',
    $defaultIsEscape   = true,
    $defaultCallback   = '=~encodeData',

    $charset           = 'UTF-8',
    $isEscape          = true,
    $callback          = '=~encodeData',

    $mimetype = array(
      'sxw'  => 'application/vnd.sun.xml.writer',
      'stw'  => 'application/vnd.sun.xml.writer.template',
      'sxg'  => 'application/vnd.sun.xml.writer.global',
      'sxc'  => 'application/vnd.sun.xml.calc',
      'stc'  => 'application/vnd.sun.xml.calc.template',
      'sxi'  => 'application/vnd.sun.xml.impress',
      'sti'  => 'application/vnd.sun.xml.impress.template',
      'sxd'  => 'application/vnd.sun.xml.draw',
      'std'  => 'application/vnd.sun.xml.draw.template',
      'sxm'  => 'application/vnd.sun.xml.math',
      'odt'  => 'application/vnd.oasis.OpenDocument.text',
      'ott'  => 'application/vnd.oasis.OpenDocument.text-template',
      'oth'  => 'application/vnd.oasis.OpenDocument.text-web',
      'odm'  => 'application/vnd.oasis.OpenDocument.text-master',
      'odg'  => 'application/vnd.oasis.OpenDocument.graphics',
      'otg'  => 'application/vnd.oasis.OpenDocument.graphics-template',
      'odp'  => 'application/vnd.oasis.OpenDocument.presentation',
      'otp'  => 'application/vnd.oasis.OpenDocument.presentation-template',
      'ods'  => 'application/vnd.oasis.OpenDocument.spreadsheet',
      'ots'  => 'application/vnd.oasis.OpenDocument.spreadsheet-template',
      'odc'  => 'application/vnd.oasis.OpenDocument.chart',
      'odf'  => 'application/vnd.oasis.OpenDocument.formula',
      'odb'  => 'application/vnd.oasis.OpenDocument.database',
      'odi'  => 'application/vnd.oasis.OpenDocument.image',
      'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
      'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
      'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
      'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
      'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
      'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
      'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
      'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
      'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
      'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
      'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
      'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
    );


  /**
   * Constructor.
   */
  public function __construct()
  {
  }


  /**
   * Create a new unique file from a source file
   *
   * @param mixed $options  The pathname of source document (empty array by default)
   */
  public function createFrom($options = array())
  {
    $this->setSourcePathname($options);

    // create an unique basename to process the office document
    $this->newUniqueBasename();

    // create an unique directory like basename to zip/unzip files
    if (!mkdir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename(), 0777, true))
    {
      throw new tinyDocException(sprintf('Can\'t make directory "%s"', $this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename()));
    }
    if (!is_dir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename()))
    {
      throw new tinyDocException(sprintf('Directory not found "%s"', $this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename()));
    }

    // copy the file source into the process dir with an unique filename
    if (!copy($this->getSourcePathname(), $this->getPathname()))
    {
      if (!file_exists($this->getSourcePathname()))
      {
        throw new tinyDocException(sprintf('Can\'t copy file, source file not found "%s"', $this->getSourcePathname()));
      }
      else
      {
        throw new tinyDocException(sprintf('Can\'t copy file from "%s" to "%s"', $this->getSourcePathname(), $this->getPathname()));
      }
    }

  }


  /**
   * Load the XML file from the current process file as a TBS template
   *
   * @param string $xmlFilename  The XML file (content.xml by default)
   */
  public function loadXml($xmlFilename = 'content.xml')
  {
    $this->setXmlFilename($xmlFilename);

    // unzip the XML file into the current basename process dir
    switch($this->getZipMethod())
    {
      case 'ziparchive':
        $zip = new ZipArchive();
        if ($zip->open($this->getPathname()) === true)
        {
          $zip->extractTo($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR, array($this->getXmlFilename()));
          $zip->close();
        }
        break;

      case 'pclzip':
        require_once('pclzip.lib.php');
        $zip = new PclZip($this->getPathname());
        $zip->extract(PCLZIP_OPT_PATH,$this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR,
            PCLZIP_OPT_BY_NAME,$this->getXmlFilename());
        break;

      case 'shell':
      default:
        $cmd = $this->getUnzipBinary();
        $cmd.= ' '.escapeshellarg($this->getPathname());
        $cmd.= ' -d';
        $cmd.= ' '.escapeshellarg($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename());
        $cmd.= ' '.escapeshellarg($this->getXmlFilename());
        exec($cmd);
        break;
    }

    // test if the XML file exist
    if (!file_exists($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$this->getXmlFilename()))
    {
      throw new tinyDocException(sprintf('Xml file not found "%s"', $this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$this->getXmlFilename()));
    }

    // load the XML file as a TBS template
    $this->ObjectRef = $this;
    $this->LoadTemplate($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$this->getXmlFilename(), $this->getCallback());

    // work around - convert apostrophe in XML file needed for TBS functions
    $this->Source = str_replace('&apos;', '\'', $this->Source);
  }


  /**
   * Merge data with the template file
   *
   * Available options:
   *
   *  - name:       The tag name in the template ('block' by default)
   *  - type:       The tag type in the template ('field' | 'block' - 'block' by default)
   *  - data_type:  The data type ('array' by default)
   *  - charset:    The data charset ('UTF-8' by default)
   *  - is_escape:  If data are escaped (true by default)
   *  - callback:   The callback to encode data ('=~encodeData' by default)
   *
   * @param array  $options    Options
   * @param mixed  $data       Data
   */
  public function mergeXml($options = array(), $data = array())
  {
    $name      = isset($options['name'])      && is_string($options['name'])      ? $options['name']      : 'block';
    $type      = isset($options['type'])      && is_string($options['type'])      ? $options['type']      : 'block';
    $data_type = isset($options['data_type']) && is_string($options['data_type']) ? $options['data_type'] : 'array';

    $this->setCharset (isset($options['charset'])   && is_string($options['charset'])   ? $options['charset']   : $this->getDefaultCharset());
    $this->setIsEscape(isset($options['is_escape']) && is_bool($options['is_escape'])   ? $options['is_escape'] : $this->getDefaultIsEscape());
    $this->setCallback(isset($options['callback'])  && is_string($options['callback'])  ? $options['callback']  : $this->getDefaultCallback());

    switch($type)
    {
      case 'field':
        $this->MergeField($name, $data);
        break;

      case 'block':
      default:
        $this->MergeBlock($name, $data_type, $data);
        break;
    }
  }


  /**
   * Merge data with a TBS field in template
   *
   * @param string $name       TBS field name in template
   * @param mixed  $data       Data to merge
   */
  public function mergeXmlField($name, $data)
  {
    $this->mergeXml(array('name' => $name, 'type' => 'field'), $data);
  }


  /**
   * Merge data with a TBS block in template
   *
   * @param string $name       TBS block name in template
   * @param mixed  $data       Data to merge
   */
  public function mergeXmlBlock($name, $data)
  {
    $this->mergeXml(array('name' => $name, 'type' => 'block'), $data);
  }


  /**
   * Save the result of merged XML file into the current process file
   */
  public function saveXml()
  {
    // get the source result (TBS method)
    $this->Show(TBS_NOTHING);

    // update the result into the current process file
    switch($this->getZipMethod())
    {
      case 'ziparchive':

        $zip = new ZipArchive();
        if ($zip->open($this->getPathname()) === true)
        {
          $zip->addFromString($this->getXmlFilename(), $this->Source);
          $zip->close();
        }
        break;

      case 'pclzip':
        require_once('pclzip.lib.php');
        // save the merged result
        $fdw = fopen($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$this->getXmlFilename(), "w");
        fwrite($fdw, $this->Source, strlen($this->Source));
        fclose ($fdw);

        // change the current directory to basename in process dir
        $cwd = getcwd();
        chdir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR);

        $zip = new PclZip($this->getPathname());
        $zip->add($this->getXmlFilename());

        // get back current directory
        chdir($cwd);
        break;

      case 'shell':
      default:
        // save the merged result
        $fdw = fopen($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$this->getXmlFilename(), "w");
        fwrite($fdw, $this->Source, strlen($this->Source));
        fclose ($fdw);

        // change the current directory to basename in process dir
        $cwd = getcwd();
        chdir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR);

        // zip the XML file into the archive
        $cmd = $this->getZipBinary();
        $cmd.= ' -m';
        $cmd.= ' '.escapeshellarg($this->getPathname());
        $cmd.= ' '.escapeshellarg($this->getXmlFilename());
        exec($cmd);

        // get back current directory
        chdir($cwd);
        break;
    }
  }


  /**
   * Encode the data before merge
   *
   * @param  string $encodeData  The data to merge
   *
   * @return string  The encoded data
   */
  public function encodeData($encodeData)
  {
    // XML charset of OpenOffice / OpenDocument / Word 2007 is utf-8
    switch($this->getCharset())
    {
      case 'ISO 8859-1':
      case 'ISO 8859-15':
        $encodeData = utf8_encode($encodeData);
        break;
      case 'UTF-8':
      case 'UTF8':
      default:
        break;
    }

    // convert special XML chars
    $encodeData = str_replace('&' ,'&amp;',  $encodeData); // the '&' has to be the first to convert
    $encodeData = str_replace('\'' ,'&apos;', $encodeData);
    $encodeData = str_replace('"' ,'&quot;', $encodeData);

    if ($this->getIsEscape())
    {
      $encodeData = str_replace('<', '&lt;', $encodeData);
      $encodeData = str_replace('>', '&gt;', $encodeData);
    }

    // convert TAB & LF & CR to XML
    switch($this->getExtension())
    {
      case 'docm':
      case 'docx':
      case 'dotm':
      case 'dotx':
        $encodeData = str_replace("\r\n", '</w:t></w:r></w:p><w:p><w:r><w:t>', $encodeData);
        $encodeData = str_replace("\n",   '</w:t></w:r></w:p><w:p><w:r><w:t>', $encodeData);
        $encodeData = str_replace("\r",   '</w:t></w:r></w:p><w:p><w:r><w:t>', $encodeData);
        $encodeData = str_replace("\t",   '</w:t></w:r><w:r><w:tab/><w:t>'   , $encodeData);
        break;

      case 'ods':
      case 'sxc':
        $encodeData = str_replace("\r\n", '</text:p><text:p>' , $encodeData);
        $encodeData = str_replace("\n",   '</text:p><text:p>' , $encodeData);
        $encodeData = str_replace("\r",   '</text:p><text:p>' , $encodeData);
        $encodeData = str_replace("\t",   '<text:tab/>'       , $encodeData);

        // work-around for EURO caracter
        $encodeData = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC),  $encodeData);
        break;

      case 'odt':
      case 'sxw':
      default:
        $encodeData = str_replace("\r\n", '<text:line-break/>', $encodeData);
        $encodeData = str_replace("\n",   '<text:line-break/>', $encodeData);
        $encodeData = str_replace("\r",   '<text:line-break/>', $encodeData);
        $encodeData = str_replace("\t",   '<text:tab/>'       , $encodeData);

        // work-around for EURO caracter
        $encodeData = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC),  $encodeData);
        break;
    }

    // remove specials chars from 0x00 to 0x1F
    $encodeData = preg_replace('/[\x{00}-\x{1F}]+/u', '', $encodeData);

    return $encodeData;
  }


  /**
   * Create a unique id and set the basename of process file
   */
  public function newUniqueBasename()
  {
    $unique = md5(uniqid(rand(), true));
    $this->setBasename($unique);
  }


  /**
   * Close by remove the current process directory and subdirectory
   */
  public function close()
  {
    // remove the current process directory
    clearstatcache();
    if (is_dir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename()))
    {
      $this->deltree($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename());
      rmdir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename());
    }
  }


  /**
   * Remove a directory and his contents recursively
   *
   * @param string $dir  The pathname the directory
   */
  private function deltree($dir)
  {
      // changed by kevin - makes no sense on shared hosting.
      // for example open basedir settings restrict access to /
      // if (realpath($dir) == realpath(DIRECTORY_SEPARATOR))

    if (realpath($dir) == DIRECTORY_SEPARATOR)
    {
      return false;
    }

    foreach(glob($dir.DIRECTORY_SEPARATOR.'*') as $filename)
    {
      if (is_dir($filename) && !is_link($filename))
      {
        $this->deltree($filename);
        if (is_writable($filename))
        {
          rmdir($filename);
        }
      }
      else
      {
        if (is_writable($filename))
        {
          unlink($filename);
        }
      }
    }
  }


  /**
   * Remove the current process file
   */
  public function remove()
  {
    unlink($this->getPathname());
  }


  /**
   * Add an external file into the current process file
   *
   * GIF images are not supported in WORD 2007
   *
   * @param string $sourcePathname   The pathname of the source file to add
   * @param string $archivePathname  The pathname in the archive
   */
  public function addFile($sourcePathname, $archivePathname)
  {
    if (file_exists($sourcePathname))
    {
      switch($this->getZipMethod())
      {
        case 'ziparchive':
          $zip = new ZipArchive();
          if ($zip->open($this->getPathname()) === true)
          {
            // don't work - archive corrupted
            // $zip->addFile($sourcePathname, $archivePathname);

            $zip->addFromString($archivePathname, file_get_contents($sourcePathname, false));
            $zip->close();
          }

          break;

        case 'pclzip':
          require_once('pclzip.lib.php');
          $zip = new PclZip($this->getPathname());
          $zip->add($sourcePathname,
            PCLZIP_OPT_ADD_PATH,$archivePathname,
            PCLZIP_OPT_REMOVE_ALL_PATH);
          break;

        case 'shell':
        default:
          $dir = dirname($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$archivePathname);
          if (!file_exists($dir))
          {
            mkdir($dir, 0777, true);
          }

          if (copy($sourcePathname, $this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR.$archivePathname))
          {
            // change the current directory to basename in process dir
            $cwd = getcwd();
            chdir($this->getProcessDir().DIRECTORY_SEPARATOR.$this->getBasename().DIRECTORY_SEPARATOR);

            // zip the file into the archive
            $cmd = $this->getZipBinary();
            $cmd.= ' -u';
            $cmd.= ' '.escapeshellarg($this->getPathname());
            $cmd.= ' '.escapeshellarg($archivePathname);
            exec($cmd);

            // get back current directory
            chdir($cwd);
          }
          break;
      }
    }
  }


  /**
   * This function is obsolete, use parameter 'image' to merge pictures.
   *
   * Hack to change in the XML source the tags for image. Only tags with [*.src] work.
   * The TBS tag need to set with OpenOffice into the title field of the image property.
   *
   * Experimental : only for OpenDocument/OpenOffice. Image in blocks don't work with spreadsheet.
   */
  public function tagXmlImage()
  {
    $doc = new DOMDocument();
    $doc->loadXML($this->Source);
    $draw_frames = $doc->getElementsByTagName('frame'); // draw:frame

    foreach ($draw_frames as $draw_frame)
    {
      $svg_titles = $draw_frame->getElementsByTagName('title'); //svg:title
      if ($svg_titles->length > 0)
      {
        $tag = $svg_titles->item(0)->nodeValue;
        if (preg_match('/\[.*src\]/', $tag))
        {
          $draw_images = $draw_frame->getElementsByTagName('image'); // draw:image
          if ($draw_images->length > 0)
          {
            $draw_image = $draw_images->item(0);
            $draw_image->removeAttribute('href'); // xlink:href
            $draw_image->setAttribute('xlink:href', $tag);
          }
        }
      }
    }
    $this->Source = $doc->saveXML();
  }

  /**
   * Send the response
   */
  public function sendResponse($options = array())
  {
    header('Content-Type: '.$this->getMimetype());
    header('Content-Disposition: attachment; filename="'.$this->getDownloadFilename().'"');
    header('Content-Length: '.$this->getSize());
    echo $this->getContent();
  }


  /**
   * Get the basename of the current process file
   *
   * @return string The basename
   */
  public function getBasename()
  {
    return $this->processBasename;
  }


  /**
   * Get the callback method to encode data
   *
   * @return string The callback method
   */
  public function getCallback()
  {
    return $this->callback;
  }


  /**
   * Get the charset of data to be merged
   *
   * @return string The charset
   */
  public function getCharset()
  {
    return $this->charset;
  }


  /**
   * Get the binary content of the current process file
   *
   * @return stream The binary content
   */
  public function getContent()
  {
    return file_get_contents($this->getPathname(), false);
  }


  /**
   * Get the default callback method to encode data
   *
   * @return string  The default callback method
   */
  public function getDefaultCallback()
  {
    return $this->defaultCallback;
  }


  /**
   * Get the default charset of data
   *
   * @return string  The default charset
   */
  public function getDefaultCharset()
  {
    return $this->defaultCharset;
  }


  /**
   * Get the default extension of source file
   *
   * @return string The default extension
   */
  public function getDefaultExtension()
  {
    return $this->defaultExtension;
  }


  /**
   * Get the default if data are escaped
   *
   * @return string The default
   */
  public function getDefaultIsEscape()
  {
    return $this->defaultIsEscape;
  }


  /**
   * Get the filename to download as the current template by default
   *
   * @return string The download filename
   */
  public function getDownloadFilename($options = array())
  {
    return basename($this->getSourcePathname());
  }


  /**
   * Get the extension of the current process file
   *
   * @return string The extension
   */
  public function getExtension()
  {
    $info = pathinfo($this->getSourcePathname());

    return $info['extension'];
  }


  /**
   * Get the filename of the current process file
   *
   * @return string The filename
   */
  public function getFilename()
  {
    return $this->getBasename().'.'.$this->getExtension();
  }


  /**
   * Get if data are escaped
   *
   * @return boolean
   */
  public function getIsEscape()
  {
    return $this->isEscape;
  }


  /**
   * Get the mimetype of the current process file
   *
   * @return string The mimetype
   */
  public function getMimetype()
  {
    return isset($this->mimetype[$this->getExtension()]) ? $this->mimetype[$this->getExtension()] : null;
  }


  /**
   * Get the pathname of the current process file
   *
   * @return string The pathname
   */
  public function getPathname()
  {
    return $this->getProcessDir().DIRECTORY_SEPARATOR.$this->getFilename();
  }


  /**
   * Get the process directory
   *
   * @return string The process dir pathname
   */
  public function getProcessDir()
  {
    return $this->processDir;
  }


  /**
   * Get the size of the current process file
   *
   * @return int The size
   */
  public function getSize()
  {
    return filesize($this->getPathname());
  }


  /**
   * Get the source file pathname
   *
   * @return string The source file pathname
   */
  public function getSourcePathname()
  {
    return $this->sourcePathname;
  }


  /**
   * Get the unzip binary pathfile
   *
   * @return string The unzip binary
   */
  public function getUnzipBinary()
  {
    return $this->unzipBin;
  }


  /**
   * Get the current XML filename
   *
   * @return string The XML filename
   */
  public function getXmlFilename()
  {
    return $this->xmlFilename;
  }


  /**
   * Get the zip binary pathfile
   *
   * @return string The zip binary
   */
  public function getZipBinary()
  {
    return $this->zipBin;
  }


  /**
   * Get the method name to zip/unzip
   *
   * @return string The zip method name
   */
  public function getZipMethod()
  {
    return $this->zipMethod;
  }


  /**
   * Set the basename of the current process file
   *
   * @param  string $basename  The basename
   */
  public function setBasename($basename)
  {
    return $this->processBasename = $basename;
  }


  /**
   * Set the callback method to encode merged data
   *
   * @param  string $callback  The callback method (TBS syntax)
   */
  public function setCallback($callback)
  {
    $this->callback = $callback;
  }


  /**
   * Set the charset
   *
   * @param  string $charset  The charset of the data to merge (utf-8 by default)
   */
  public function setCharset($charset = 'UTF-8')
  {
    $this->charset = strtoupper($charset);
  }


  /**
   * Set if '<' & '>' in merged data are escaped by '&lt;' & '&gt;'
   *
   * BE CAREFUL TO SET TO FALSE
   *
   * @param  boolean $isEscape  Escape data (true by default)
   */
  public function setIsEscape($isEscape = true)
  {
    $this->isEscape = $isEscape;
  }


  /**
   * Set the source file pathname
   *
   * @param mixed $options  The sourcePathname
   */
  public function setSourcePathname($options = array())
  {
    $sourcePathname = $options;

    // test if file exist
    if (!file_exists($sourcePathname))
    {
      throw new tinyDocException(sprintf('File not found "%s"', $sourcePathname));
    }

    // test if file readable
    if (!is_readable($sourcePathname))
    {
      throw new tinyDocException(sprintf('File not readable "%s"', $sourcePathname));
    }

    $this->sourcePathname = $sourcePathname;
  }


  /**
   * Set the process directory
   *
   * @param  string $processDir  The pathname of the process dir
   */
  public function setProcessDir($processDir = 'tmp')
  {
      // changed by kevin - makes no sense on shared hosting.
      // for example open basedir settings restrict access to /
      // if (realpath($processDir) == realpath(DIRECTORY_SEPARATOR))
    if (realpath($processDir) == DIRECTORY_SEPARATOR)
    {
      throw new tinyDocException(sprintf('Could not use the root for the process directory "%s"', $processDir));
    }
    if (!is_dir(realpath($processDir)))
    {
      throw new tinyDocException(sprintf('Process directory not found "%s"', $processDir));
    }
    if (!is_writable(realpath($processDir)))
    {
      throw new tinyDocException(sprintf('Process directory not writable "%s"', $processDir));
    }

    $this->processDir = rtrim(realpath($processDir), DIRECTORY_SEPARATOR);
  }


  /**
   * Fix the unzip binary
   *
   * @param  string  $unzipBin  The pathname of the unzip binary (unzip by default)
   */
  public function setUnzipBinary($unzipBinary = 'unzip')
  {
    if ($this->getZipMethod() == 'shell')
    {
      $unzipBinary = self::escapeShellCommand($unzipBinary);

      if (strlen(shell_exec($unzipBinary.' -h')) == 0)
      {
        throw new tinyDocException(sprintf('"%s" not executable', $unzipBinary));
      }

      $this->unzipBin = $unzipBinary;
    }
  }


  /**
   * Set the XML filename of the current template
   *
   * @param string The pathname of XML filename (content.xml by default)
   */
  public function setXmlFilename($xmlFilename = 'content.xml')
  {
    $this->xmlFilename = $xmlFilename;
  }


  /**
   * Fix the zip binary
   *
   * @param  string  $zipBin  The pathname of the zip binary (zip by default)
   */
  public function setZipBinary($zipBinary = 'zip')
  {
    if ($this->getZipMethod() == 'shell')
    {
      $zipBinary = self::escapeShellCommand($zipBinary);

      if (strlen(shell_exec($zipBinary.' -h')) == 0)
      {
        throw new tinyDocException(sprintf('"%s" not executable', $zipBinary));
      }

      $this->zipBin = $zipBinary;
    }
  }


  /**
   * Fix the method to zip/unzip file.
   *
   * @param  string  $zipMethod  The method are 'shell' or 'ziparchive' (shell by default)
   */
  public function setZipMethod($zipMethod = 'shell')
  {
    $method = strtolower($zipMethod);

    if (!in_array($method, array('shell', 'ziparchive', 'pclzip')))
    {
      throw new tinyDocException(sprintf('Zip method "%s" need to be \'shell\' or \'ziparchive\'', $method));
    }

    if ($method == 'ziparchive' && !class_exists('ZipArchive'))
    {
      throw new tinyDocException('Zip extension not loaded - check your php settings, PHP 5.2 minimum with zip extension is required');
    }

    $this->zipMethod = $method;
  }


  /**
   * Redefinition of TBS method to make an exception when an alert occured
   *
   * @param  string $Src       TBS subject ???
   * @param  string $Msg       TBS message ???
   * @param  string $NoErrMsg  TBS no error message ???
   * @param  string $SrcType   The TBS source type ???
   */
  public function meth_Misc_Alert($Src, $Msg, $NoErrMsg=false, $SrcType=false)
  {
    throw new tinyDocException(sprintf('%s', $Msg));
  }


  /**
   * Redefinition of TBS method to add parameter 'cell' to set value with native OpenDocument format in spreadsheet
   *
   * refactoring code from egroupware/perp_api/inc/report/ooo.inc.php
   *
   * @param  string $Txt       TBS source
   * @param  string $Loc       TBS locator
   * @param  string $Value     TBS data to merge
   * @param  string $CheckSub  TBS checksub ???
   */
  public function meth_Locator_Replace(&$Txt, &$Loc, &$Value, $CheckSub)
  {

    if (!isset($Loc->PrmLst['type']) && !isset($Loc->PrmLst['image']) && !isset($Loc->PrmLst['link']))
    {
      return parent::meth_Locator_Replace($Txt, $Loc, $Value, $CheckSub);
    }

    // keep 'Loc' position for the end
    $posBeg = $Loc->PosBeg;
    $posEnd = $Loc->PosEnd;

    // get data
    $data = isset($Value) ? $Value : null;
    foreach($Loc->SubLst as $sub)
    {
      $data = isset($Value[$sub]) ? $Value[$sub] : null ;
    }

    // ----- parameter = type
    if (isset($Loc->PrmLst['type']))
    {
      if ($data == '')
      {
        $Txt = substr_replace($Txt, '', $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1 );
        $Loc->PosBeg   = $posBeg;
        $Loc->PosEnd   = $posEnd;
        return $Loc->PosBeg;
      }

      // get container enlarged to table:table-cell
      $Loc->Enlarged    = $this->f_Loc_EnlargeToStr($Txt, $Loc, '<table:table-cell' ,'/table:table-cell>');
      $container    = substr($Txt, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);

      if ($container == '')
      {
        throw new tinyDocException(sprintf('<table:table-cell not found in document "%s"', $this->getXmlFilename()));
      }

      // OpenOffice attributes cell - see : http://books.evc-cit.info/odbook/ch05.html#table-cells-section
      switch($Loc->PrmLst['type'])
      {
        case 'datetime':
        case 'date':
        case 'dt':
        case 'd':
          $attribute = sprintf('office:value-type="date" office:date-value="%s"', str_replace(' ', 'T', $data));
          break;

        case 'time':
        case 't':
          list($h, $m, $s) = split(":", $data);
          $attribute = sprintf('office:value-type="time" office:time-value="PT%sH%sM%sS"', $h, $m, $s);
          break;

        case 'percentage':
        case 'percent':
        case 'p':
          $attribute = sprintf('office:value-type="percentage" office:value="%s"', $data);
          break;

        case 'currency':
        case 'cur':
        case 'c':
          //$attribute = sprintf('office:value-type="currency" office:currency="EUR" office:value="%s"', $data); // still not necessary to fix the currency
          $attribute = sprintf('office:value-type="currency" office:value="%s"', $data);
          break;

        case 'number':
        case 'n':
        case 'float':
        case 'f':
          $attribute = sprintf('office:value-type="float" office:value="%s"', $data);
          break;

        case 'int':
        case 'i':
          $attribute = sprintf('office:value-type="float" office:value="%d"', $data);
          break;

        default:
        case 'string':
        case 's':
          $attribute = sprintf('office:value-type="string"');
          break;
      }

      // new container
      $newContainer = preg_replace('/office:value-type="string"/', $attribute, $container);

      // replace the new cell containter in the main Txt
      $Txt = substr_replace($Txt, $newContainer, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1 );

      // correct 'Loc' to include the change of the new cell container
      $delta = strlen($newContainer) - strlen($container);
      $Loc->PosBeg   = $posBeg + $delta;
      $Loc->PosEnd   = $posEnd + $delta;
      $Loc->Enlarged = null;
    }


    // ----- parameter = image
    if (isset($Loc->PrmLst['image']))
    {
      // get container enlarged to draw:frame
      $Loc->Enlarged = $this->f_Loc_EnlargeToStr($Txt, $Loc, '<draw:frame' ,'/draw:frame>');
      $container = substr($Txt, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);

      if ($container == '')
      {
        throw new tinyDocException(sprintf('<draw:frame not found in document "%s"', $this->getXmlFilename()));
      }

      // test if data is empty or if file not exists
      if ($data == '' || !file_exists($data))
      {
        $Txt = substr_replace($Txt, '', $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1 );
        $Loc->PosBeg   = $posBeg;
        $Loc->PosEnd   = $posEnd;
        $Loc->Enlarged = null;
        return $Loc->PosBeg;
      }

      $picture = 'Pictures/'.basename($data);

      // image size
      $size = @getimagesize($data);
      if ($size === false)
      {
        throw new tinyDocException(sprintf('Invalid image format "%"', $data));
      }
      else
      {
        list ($width, $height) = $size;
      }


      // image ratio
      $ratio = 1;

      switch(strtolower($Loc->PrmLst['image']))
      {
        case 'fit':
          if (preg_match('/svg:width="(.*?)(cm|in|mm|px)" svg:height="(.*?)(cm|in|mm|px)"/', $container, $matches))
          {
            $ratio_w = self::convertToCm($matches[1], $matches[2]) / self::convertToCm($width, 'px');
            $ratio_h = self::convertToCm($matches[3], $matches[4]) / self::convertToCm($height,'px');
            $ratio = min($ratio_w, $ratio_h);
          }
          break;
        case 'max':
          if (preg_match('/svg:width="(.*?)(cm|in|mm|px)" svg:height="(.*?)(cm|in|mm|px)"/', $container, $matches))
          {
            $ratio_w = self::convertToCm($matches[1], $matches[2]) / self::convertToCm($width, 'px');
            $ratio_h = self::convertToCm($matches[3], $matches[4]) / self::convertToCm($height,'px');
            $ratio = min(1, $ratio_w, $ratio_h);
          }
          break;
        default:
          if (preg_match('/([0-9\.]*)%/', $Loc->PrmLst['image'], $matches) > 0)
          {
            $ratio = $matches[1] / 100;
          }
          break;
      }

      // replace values
      $newContainer = $container;
      $newContainer = preg_replace('/svg:width="(.*?)"/' , sprintf('svg:width="%scm"' , self::convertToCm($width, 'px') * $ratio), $newContainer);
      $newContainer = preg_replace('/svg:height="(.*?)"/', sprintf('svg:height="%scm"', self::convertToCm($height,'px') * $ratio), $newContainer);
      $newContainer = preg_replace('/xlink:href="(.*?)"/', sprintf('xlink:href="%s"'  , $picture), $newContainer);

      // add file
      $this->addFile($data, $picture);

      // replace the new cell containter in the main Txt
      $Txt = substr_replace($Txt, $newContainer, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);

      $Loc->PosBeg   = $posBeg;
      $Loc->PosEnd   = $posEnd;
      $Loc->Enlarged = null;
    }


    // ----- parameter = link
    if (isset($Loc->PrmLst['link']))
    {
      if ($data == '')
      {
        $Txt = substr_replace($Txt, '', $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1 );
        $Loc->PosBeg   = $posBeg;
        $Loc->PosEnd   = $posEnd;
        return $Loc->PosBeg;
      }

      $container = substr($Txt, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);
      $title = ($Loc->PrmLst['link'] != '1' ? $Loc->PrmLst['link'] : $data);
      $newContainer = sprintf('<text:a xlink:type="simple" xlink:href="%s">%s</text:a>', $data, $title);

      $Txt = substr_replace($Txt, $newContainer, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);

      // before return, restore 'Loc' with beginning values (to work with block)
      $Loc->PosBeg   = $posBeg;
      $Loc->PosEnd   = $posEnd;
      return $Loc->PosEnd;
    }

    // call the parent method to insert the value
    $ret = parent::meth_Locator_Replace($Txt, $Loc, $Value, $CheckSub);

    // before return, restore 'Loc' with beginning values (to work with block)
    $Loc->PosBeg = $posBeg;
    $Loc->PosEnd = $posEnd;

    return $ret;
  }


  public static function convertToCm($value, $unit)
  {
    switch(strtolower($unit))
    {
      case 'px':
        $returnValue = $value * self::INCH_TO_CM / self::PICTURE_DPI;
        break;
      case 'in':
        $returnValue = $value * self::INCH_TO_CM;
        break;
      case 'mm':
        $returnValue = $value / 10;
        break;
      default;
      case 'cm':
        $returnValue = $value;
        break;
    }

    return $returnValue;
  }


  public static function escapeShellCommand($command)
  {
    if (strpos($command, ' ') !== false)
    {
      $command = (strpos($command, '"') === 0 ? '' : '"').$command;
      $command = $command.((strrpos($command, '"') == strlen($command)-1) ? '' : '"');
    }
    return $command;
  }

}



/**
 * tinyDocException exception class
 *
 * @package    tinyDoc
 * @subpackage tinyDocException
 * @author     Olivier Loynet <tinydoc@googlegroups.com>
 * @version    $Id$
 */
class tinyDocException extends Exception
{
}
