<?php

namespace Orth\IndexBundle\Classes;

class ExtractCommonWords
{
    function ExtractCommonWords($string){
      $stopWords = array("ab","aber","abgesehen","alle","allein","aller","alles","als","am","an","andere","anderen","anderenfalls","anderer","anderes","anstatt","auch","auf","aus","aussen","außen","ausser","außer","ausserdem","außerdem","außerhalb","ausserhalb","behalten","bei","beide","beiden","beider","beides","beinahe","bevor","bin","bis","bist","bitte","da","daher","danach","dann","darueber","darüber","darueberhinaus","darüberhinaus","darum","das","dass","daß","dem","den","der","des","deshalb","die","diese","diesem","diesen","dieser","dieses","dort","duerfte","duerften","duerftest","duerftet","dürfte","dürften","dürftest","dürftet","durch","durfte","durften","durftest","durftet","ein","eine","einem","einen","einer","eines","einige","einiger","einiges","entgegen","entweder","erscheinen","es","etwas","fast","fertig","fort","fuer","für","gegen","gegenueber","gegenüber","gehalten","geht","gemacht","gemaess","gemäß","genug","getan","getrennt","gewesen","gruendlich","gründlich","habe","haben","habt","haeufig","häufig","hast","hat","hatte","hatten","hattest","hattet","hier","hindurch","hintendran","hinter","hinunter","ich","ihm","ihnen","ihr","ihre","ihrem","ihren","ihrer","ihres","ihrige","ihrigen","ihriges","immer","in","indem","innerhalb","innerlich","irgendetwas","irgendwelche","irgendwenn","irgendwo","irgendwohin","ist","jede","jedem","jeden","jeder","jedes","jedoch","jemals","jemand","jemandem","jemanden","jemandes","jene","jung","junge","jungem","jungen","junger","junges","kann","kannst","kaum","koennen","koennt","koennte","koennten","koenntest","koenntet","können","könnt","könnte","könnten","könntest","könntet","konnte","konnten","konntest","konntet","machen","macht","machte","mehr","mehrere","mein","meine","meinem","meinen","meiner","meines","meistens","mich","mir","mit","muessen","müssen","muesst","müßt","muß","muss","musst","mußt","nach","nachdem","naechste","nächste","nebenan","nein","nichts","niemand","niemandem","niemanden","niemandes","nirgendwo","nur","oben","obwohl","oder","oft","ohne","pro","sagte","sagten","sagtest","sagtet","scheinen","sehr","sei","seid","seien","seiest","seiet","sein","seine","seinem","seinen","seiner","seines","seit","selbst","sich","sie","sind","so","sogar","solche","solchem","solchen","solcher","solches","sollte","sollten","solltest","solltet","sondern","statt","stets","tatsächlich","tatsaechlich","tief","tun","tut","ueber","über","ueberall","überll","um","und","uns","unser","unsere","unserem","unseren","unserer","unseres","unten","unter","unterhalb","usw","viel","viele","vielleicht","von","vor","vorbei","vorher","vorueber","vorüber","waehrend","während","wann","war","waren","warst","wart","was","weder","wegen","weil","weit","weiter","weitere","weiterem","weiteren","weiterer","weiteres","welche","welchem","welchen","welcher","welches","wem","wen","wenige","wenn","wer","werde","werden","werdet","wessen","wie","wieder","wir","wird","wirklich","wirst","wo","wohin","wuerde","wuerden","wuerdest","wuerdet","würde","würden","würdest","würdet","wurde","wurden","wurdest","wurdet","ziemlich","zu","zum","zur","zusammen","zwischen");
   
      $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
      $string = trim($string); // trim the string
      $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
      $string = strtolower($string); // make it lowercase
   
      preg_match_all('/\b.*?\b/i', $string, $matchWords);
      $matchWords = $matchWords[0];
      
      foreach ( $matchWords as $key=>$item ) {
          if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {
              unset($matchWords[$key]);
          }
      }   
      $wordCountArr = array();
      if ( is_array($matchWords) ) {
          foreach ( $matchWords as $key => $val ) {
              $val = strtolower($val);
              if ( isset($wordCountArr[$val]) ) {
                  $wordCountArr[$val]++;
              } else {
                  $wordCountArr[$val] = 1;
              }
          }
      }
      arsort($wordCountArr);
      $wordCountArr = array_slice($wordCountArr, 0, 10);
      return $wordCountArr;
    }
}