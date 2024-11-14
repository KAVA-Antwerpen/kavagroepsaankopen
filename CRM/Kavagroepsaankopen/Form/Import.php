<?php

use CRM_Kavagroepsaankopen_ExtensionUtil as E;

class CRM_Kavagroepsaankopen_Form_Import extends CRM_Core_Form {

  public function buildQuickForm(): void {
    $this->add('file', 'uploadFile', 'CSV-bestand', ['size=30', 'maxlength=255'], TRUE);

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    try {
      // get the selected file
      $tmpFileName = $this->_submitFiles['uploadFile']['tmp_name'];
      if (!$tmpFileName) {
        throw new Exception('Kan het bestand niet. Misschien is het te groot?');
      }

      [$numImported, $numSkipped] = CRM_Kavagroepsaankopen_Import::fromFile($tmpFileName);

      CRM_Core_Session::setStatus("Aantal geïmporteerd: $numImported<br>Aantal overgeslagen: $numSkipped", 'Import gelukt', 'success');
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), 'Fout', 'error');
    }

    parent::postProcess();
  }

  public function getRenderableElementNames(): array {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
