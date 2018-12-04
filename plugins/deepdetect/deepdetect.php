<?php

/**
 * Plugin deepdetect.
 * Adds the addlink input on the linklist page.
 */

/**
 * When linklist is displayed, add play videos to header's toolbar.
 *
 * @param array $data - header data.
 *
 * @return mixed - header data with addlink toolbar item.
 */
function hook_deepdetect_render_editlink($data)
{
  $url = "http://10.10.77.61:18104/api/eris/predict";
  $params = array(
    "service" => "alx_classif_convnet",
    "parameters" => array (
      "output" => array (
        "confidence_threshold" => 0.04,
        "best" => 4
      ),
      "mllib" => array (
        "gpu" => True
      )
    ),
    "data" => array (
      $data["link"]["url"]
    )
  );

	$ch = curl_init( $url );
	# Setup request to send json via POST.
	$payload = json_encode($params);
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  # Return response instead of printing.
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  # Send request.
  $result = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($result, true);

  $buttons = "";
  foreach ($json["body"]["predictions"][0]["classes"] as &$prediction) {
    //$buttons .= " <a class='pure-button'>" + $prediction["cat"] + "</a>";
    $buttons .= " <a class='pure-button deepdetect' onClick='document.getElementById(\"lf_tags\").value += \" " . $prediction["cat"] . "\";'>" . $prediction["cat"] . " - " . intval($prediction["prob"] * 100) . "%</a>";
  }

  $data['edit_link_plugin'][] = $buttons;

  return $data;
}

/**
 * This function is never called, but contains translation calls for GNU gettext extraction.
 */
function addlink_toolbar_dummy_translation()
{
    // meta
    t('Adds the addlink input on the linklist page.');
}
