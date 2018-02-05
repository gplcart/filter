<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
return array(
    'minimal' => array(
        'name' => 'Minimal configuration', // @text
        'description' => 'Minimal configuration for untrusted users', // @text
        'status' => false,
        'role_id' => array(),
        'module' => 'filter',
        'data' => array(
            'AutoFormat.DisplayLinkURI' => true,
            'AutoFormat.RemoveEmpty' => true,
            'HTML.Allowed' => 'strong,em,p,b,s,i,a[href|title],img[src|alt],'
                . 'blockquote,code,pre,del,ul,ol,li'
        )
    ),
    'advanced' => array(
        'name' => 'Advanced configuration', // @text
        'description' => 'Advanced configuration for trusted users, e.g content managers', // @text
        'status' => false,
        'role_id' => array(),
        'module' => 'filter',
        'data' => array(
            'AutoFormat.Linkify' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            'AutoFormat.RemoveEmpty' => true,
            'HTML.Nofollow' => true,
            'HTML.Allowed' => 'div,table,tr,td,tbody,tfoot,thead,th,strong,'
                . 'em,p[style],b,s,i,h2,h3,h4,h5,hr,br,span[style],a[href|title],'
                . 'img[width|height|alt|src],blockquote,code,pre,del,kbd,'
                . 'cite,dt,dl,dd,sup,sub,ul,ol,li',
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,'
                . 'font-family,text-decoration,padding-left,color,'
                . 'background-color,text-align',
            'HTML.FlashAllowFullScreen' => true,
            'HTML.SafeObject' => true,
            'HTML.SafeEmbed' => true,
            'HTML.Trusted' => true,
            'Output.FlashCompat' => true
        )
    ),
    'maximal' => array(
        'name' => 'Maximal configuration', // @text
        'description' => 'Maximal configuration for experienced and trusted users, e.g superadmin', // @text
        'status' => false,
        'role_id' => array(),
        'module' => 'filter',
        'data' => array(
            'AutoFormat.Linkify' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => false,
            'AutoFormat.RemoveEmpty' => true,
            'HTML.Allowed' => 'div,table,tr,td,tbody,tfoot,thead,th,strong,'
                . 'em,p[style],b,s,i,h2,h3,h4,h5,hr,br,span[style],a[href|title],'
                . 'img[width|height|alt|src],blockquote,code,pre,del,kbd,'
                . 'cite,dt,dl,dd,sup,sub,ul,ol,li',
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,'
                . 'font-family,text-decoration,padding-left,color,'
                . 'background-color,text-align',
            'HTML.FlashAllowFullScreen' => true,
            'HTML.SafeObject' => true,
            'HTML.SafeEmbed' => true,
            'HTML.Trusted' => true,
            'Output.FlashCompat' => true,
            'Attr.AllowedFrameTargets' => array('_blank', '_self', '_parent', '_top')
        )
    )
);

