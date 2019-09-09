<?php
/**
 * REDCap External Module: ValAR
 * Module for validating field against REGEX
 * @author Quentin Cavaillé, Institut Bergonié
 */

namespace IB\RegexValidate;

use ExternalModules\AbstractExternalModule;
use REDCap;

class RegExpValidate extends AbstractExternalModule{
    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance){
       ?>
            <script type="text/javascript">
                (function($, window, document) {
                    function check_regex(field, regex, message){
                        $(field).on('change', function(){
                            if (!new RegExp(regex,"g").test($(field).val())){
                                $(field).attr("style", "font-weight: bold; background-color: rgb(255, 183, 190);");
                                alert(message);
                            }
                            else{
                                $(event.target).attr("style", "font-weight: normal; background-color: rgb(255, 255, 255);");
                            }
                        })
                    }
                    <?php
                    $ins = REDCap::getDataDictionary('array', false, true, $this->instrument);
                    $instruments = array();
                    foreach ( $ins as $fieldName => $fieldContent){
                        $tmp = $fieldContent['field_annotation'];
                        $tmp = explode("\n", $tmp);
                        $all_tags_one_line = join(" ",array_map('trim', $tmp));
                        preg_match_all('/@(\w+)=((?:".+") |(?:\S+))/', $all_tags_one_line, $matches);
                        foreach($matches[1] as $index => $tag){
                            if ($tag == "REGEX"){
                                $instruments[$fieldName]['regex']=str_replace("\u0020", " ", rtrim($matches[2][$index]));
                            }
                            if ($tag == "REGEX_MSG"){
                                $instruments[$fieldName]['regex_msg']=rtrim($matches[2][$index]);
                             }
                        }
                    }
                    ?>
                    var instruments = JSON.parse(`<?php print(json_encode($instruments)); ?>`)
                    $.each(instruments, function(index, value){
                        var field = $("input[name="+index+"]")[0];
                        var regex = value['regex']
                        var regex_msg = value['regex_msg']
                        check_regex(field, regex, regex_msg);

                    })
                    <?php
                    ?>
                }(window.jQuery, window, document));
            </script>
       <?php
    }

}
?>
