<?php

function generateDisplayNameAppend($name) {
    $parts = explode("/", $name);
    if (!isset($parts[1])) {
      return '';
    }
    $name = str_replace('_', ' ', $parts[1]);
    $name = str_replace('St ', 'St. ', $name);
    return " - {$name}";
}

function generateTimeZoneListJson()
{
    $timezones = DateTimeZone::listIdentifiers();

    $timezoneOffsets = array();
    foreach( $timezones as $timezone )
    {
        $tz = new DateTimeZone($timezone);
        $timezoneOffsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by offset
    asort($timezoneOffsets);

    $timezoneList = array();
    foreach( $timezoneOffsets as $timezone => $offset )
    {
        $offsetPrefix = $offset < 0 ? '-' : '+';
        $formattedOffset = gmdate( 'H:i', abs($offset) );

        $displayName = \IntlTimeZone::createTimeZone($timezone)->getDisplayName();

        $prettyOffset = "GMT${offsetPrefix}${formattedOffset}";
        $displayNameAppend = generateDisplayNameAppend($timezone);

        $tz = [];
        $tz['label'] = "(${prettyOffset}) {$displayName}{$displayNameAppend}";
        $tz['name'] = $timezone;

        $timezoneList[] = $tz;
    }

    return $timezoneList;
}

$json = json_encode(generateTimeZoneListJson(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
$json = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json);
$json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $json);
$json = str_replace('"', '\'', $json);
$json = str_replace(':\'', ': \'', $json);

echo '<pre>';
print_r($json);
echo '</pre>';

?>
