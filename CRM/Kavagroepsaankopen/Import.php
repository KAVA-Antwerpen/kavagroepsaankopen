<?php

class CRM_Kavagroepsaankopen_Import {

  public static function fromFile(string $file): array {
    if (!file_exists($file)) {
      throw new Exception("$file does not exist");
    }

    $f = fopen($file, "r");
    if ($f === FALSE) {
      throw new Exception("Cannot open $file");
    }

    $i = 0;
    $numImported = 0;
    $numSkipped = 0;
    $colIndexes = [];
    while (($data = fgetcsv($f, 10000, ";")) !== FALSE) {
      if ($i == 0) {
        $colIndexes = self::validateHeader($data);
      }
      else {
        $ret = self::importLine($i, $colIndexes, $data);
        if ($ret === TRUE) {
          $numImported++;
        }
        else {
          $numSkipped++;
        }
      }

      $i++;
    }

    fclose($f);

    return [$numImported, $numSkipped];
  }

  public static function createActivity(string $date, int $contactId, string $subject): bool {
    if (self::existsActivity($date, $contactId, $subject)) {
      return FALSE;
    }

    \Civi\Api4\Activity::create(FALSE)
      ->addValue('activity_type_id:label', 'Groepsaankoop')
      ->addValue('subject', $subject)
      ->addValue('target_contact_id', $contactId)
      ->addValue('source_contact_id', 1)
      ->addValue('activity_date_time', $date)
      ->execute();

    return TRUE;
  }

  private static function getContactIdFromApb(string $apb): int {
    $activity = \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id')
      ->addWhere('contact_apotheekuitbating.APB_nummer', '=', $apb)
      ->addWhere('is_deleted', '=', FALSE)
      ->addOrderBy('contact_apotheekuitbating.Overname', 'DESC')
      ->execute()
      ->first();

    if ($activity) {
      return $activity['id'];
    }
    else {
      return 0;
    }
  }

  private static function existsActivity(string $date, int $contactId, string $subject): bool {
    [$from, $to] = self::getDayRange($date);

    $activity = \Civi\Api4\Activity::get(FALSE)
      ->addSelect('id')
      ->addWhere('activity_type_id:label', '=','Groepsaankoop')
      ->addWhere('subject', '=', $subject)
      ->addWhere('target_contact_id', '=', $contactId)
      ->addWhere('activity_date_time', '>=', $from)
      ->addWhere('activity_date_time', '<=', $to)
      ->execute()
      ->first();

    if ($activity) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  private static function getDayRange(string $date): array {
    $d = substr($date, 0, 10);

    return ["$d 00:00:00", "$d 23:59:59"];
  }

  public static function validateHeader(array $data): array {
    $colIndexes = [];

    if (count($data) < 3) {
      throw new Exception('Expected at least 3 columns');
    }

    for ($i = 0; $i < count($data); $i++) {
      switch ($data[$i]) {
        case 'Aangemaakt':
          $colIndexes['date'] = $i;
          break;
        case 'APB nummer':
          $colIndexes['apb'] = $i;
          break;
        case 'civi_referentie':
          $colIndexes['reference'] = $i;
          break;
      }
    }

    if (!array_key_exists('date', $colIndexes)) {
      throw new Exception('Could not find date column. Column header must be: Aangemaakt');
    }

    if (!array_key_exists('apb', $colIndexes)) {
      throw new Exception('Could not find APB number column. Column header must be: APB nummer');
    }

    if (!array_key_exists('reference', $colIndexes)) {
      throw new Exception('Could not find civi reference column. Column header must be: civi_referentie');
    }

    return $colIndexes;
  }

  public static function importLine(int $lineNumber, array $colIndexes, array $data): bool {
    if (empty($data[$colIndexes['date']])) {
      throw new Exception("Empty date on line $lineNumber");
    }

    if (empty($data[$colIndexes['apb']])) {
      throw new Exception("Empty APB number on line $lineNumber");
    }

    if (empty($data[$colIndexes['reference']])) {
      throw new Exception("Empty civi reference on line $lineNumber");
    }

    $contactId = self::getContactIdFromApb($data[$colIndexes['apb']]);
    if (!$contactId) {
      CRM_Core_Session::setStatus('Could not find contact with APB number = ' . $data[$colIndexes['apb']] . " on line $lineNumber", '', 'error');
      return FALSE;
    }

    return self::createActivity($data[$colIndexes['date']], $contactId, $data[$colIndexes['reference']]);
  }
}
