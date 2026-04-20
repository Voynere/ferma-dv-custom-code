<?php


namespace WCSTORES\WC\MS\Support\Wordpress;


use WCSTORES\WC\MS\Support\Main\Translit;

class Attribute
{
    /**
     * @param $sLabel
     * @return string|string[]
     */
    static function getAttributeLabel($sLabel)
    {
        $aoTaxonomies = get_taxonomies([], 'objects ');

        foreach ($aoTaxonomies as $oTaxonomy) {
            if (is_object($oTaxonomy) and isset($oTaxonomy->labels->singular_name) and $oTaxonomy->labels->singular_name == $sLabel) {
                return str_replace("pa_", "", $oTaxonomy->name);
            }
        }

        return self::getTranslitLabel($sLabel);
    }

    /**
     * @param $sLabel
     * @return string
     */
    static function getTranslitLabel($sLabel)
    {
        if (strlen(wc_sanitize_taxonomy_name($sLabel)) > 20) {
            $sLabel = Translit::translit($sLabel);
            $sLabel = substr($sLabel, 0, 20);
            $sLabel = rtrim($sLabel, "!,.-");
        }

        return $sLabel;
    }

    /**
     * @param $label
     * @param $value
     * @return string
     */
    static function getAttributeValue($label, $value)
    {
        $oTerm = get_term_by('name', $value, 'pa_' . $label);
        if( is_object($oTerm) and isset($oTerm->slug)){
            return  $oTerm->slug;
        }

        return $value;// slug без pa_

    }


}