<?php
/**
 * REDCap External Module: ValAR
 * Module for validating field against REGEX
 * @author Quentin Cavaillé, Institut Bergonié, Yec'han Laizet
 */

namespace InstitutBergonie\RegExpValidate;

use ExternalModules\AbstractExternalModule;
use REDCap;

class RegExpValidate extends AbstractExternalModule{
    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance){
       ?>
            <script type="text/javascript">
                (function($, window, document) {
                    function check_regex(field, regex, message){
                        $(field).attr("pattern", regex)
                        $(field).attr("title", message)
                        $(field).on('change', function(){
                            if (!$(field)[0].validity.valid){
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
                    $fields = array();
                    foreach ( $ins as $fieldName => $fieldContent){
                        $tmp = $fieldContent['field_annotation'];
                        $tmp = explode("\n", $tmp);
                        $all_tags_one_line = join(" ",array_map('trim', $tmp));
                        preg_match_all('/@(\w+)=((?:".+") |(?:\S+))/', $all_tags_one_line, $matches, PREG_SET_ORDER, 0);
                        foreach($matches as $index => $match){
                            if ($match[1]== "REGEX"){
                                $fields[$fieldName]['regex']=str_replace("\u0020", " ", rtrim($match[2]));
                            }
                            $fields[$fieldName]['regex_msg']="";
                            if ($match[1] == "REGEX_MSG"){
                                $regex_msg = rtrim($match[2]);
                                $regex_msg = str_replace('"', '', $regex_msg);
                                $fields[$fieldName]['regex_msg'] = $regex_msg;
                            }
                        }
                    }
                    ?>
                    var fields = JSON.parse(`<?php print(json_encode($fields)); ?>`)
                    $.each(fields, function(index, value){
                        var field = $("input[name="+index+"]")[0];
                        var regex = value['regex']
                        var regex_msg = value['regex_msg']
                        if (regex_msg ==""){
                            regex_msg = "Ce champ devrait être égale a : "+regex
                        }
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
