<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | The configuration settings array is passed directly to HTMLPurifier.
    |
    | Feel free to add / remove / customize these attributes as you wish.
    |
    | Documentation: http://htmlpurifier.org/live/configdoc/plain.html
    |
    */

    'settings' => [
        'HTML.Doctype' => 'XHTML 1.0 Transitional',
        'Attr.EnableID' => true,
        'Attr.ID.HTML5' => true,

        'HTML.Allowed' => 'div[class|style|id],b,strong,i[class|style|id]'
            . ',em,a[class|style|id|title|target|href],ul[class|style|id],ol[class|style|id],li[class|style|id]'
            . ',p[class|style|id],br,span[class|style|id],img[width|height|alt|src|class|style|id]'
            . ',iframe[src|width|height|frameborder|scrolling|class|style|id|data-aa],h1[class|style|id],h2[class|style|id],h3[class|style|id],h4[class|style|id],h5[class|style|id],h6[class|style|id]'
            . ',dt[class|style|id],dl[class|style|id]',
        'Attr.AllowedFrameTargets' => ['_blank'],
        "HTML.SafeIframe" => 'true',
        "URI.SafeIframeRegexp" => "%^(http://|https://|//)(mellowads.com|coinurl.com|a-ads.com|ad.a-ads.com|bee-ads.com)%",

        /*
        |--------------------------------------------------------------------------
        | Core.Encoding
        |--------------------------------------------------------------------------
        |
        | The encoding to convert input to.
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#Core.Encoding
        |
        */

        'Core.Encoding' => 'utf-8',

        /*
        |--------------------------------------------------------------------------
        | Core.SerializerPath
        |--------------------------------------------------------------------------
        |
        | The HTML purifier serializer cache path.
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#Cache.SerializerPath
        |
        */

        'Cache.SerializerPath' => storage_path('purify'),

        /*
        |--------------------------------------------------------------------------
        | HTML.ForbiddenElements
        |--------------------------------------------------------------------------
        |
        | The forbidden HTML elements. Elements that are listed in
        | this string will be removed, however their content will remain.
        |
        | For example if 'p' is inside the string, the string: '<p>Test</p>',
        |
        | Will be cleaned to: 'Test'
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#HTML.ForbiddenElements
        |
        */

        'HTML.ForbiddenElements' => '',

        /*
        |--------------------------------------------------------------------------
        | CSS.AllowedProperties
        |--------------------------------------------------------------------------
        |
        | The Allowed CSS properties.
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#CSS.AllowedProperties
        |
        */

        'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',

        /*
        |--------------------------------------------------------------------------
        | AutoFormat.AutoParagraph
        |--------------------------------------------------------------------------
        |
        | The Allowed CSS properties.
        |
        | This directive turns on auto-paragraphing, where double
        | newlines are converted in to paragraphs whenever possible.
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#AutoFormat.AutoParagraph
        |
        */

        'AutoFormat.AutoParagraph' => false,

        /*
        |--------------------------------------------------------------------------
        | AutoFormat.RemoveEmpty
        |--------------------------------------------------------------------------
        |
        | When enabled, HTML Purifier will attempt to remove empty
        | elements that contribute no semantic information to the document.
        |
        | http://htmlpurifier.org/live/configdoc/plain.html#AutoFormat.RemoveEmpty
        |
        */

        'AutoFormat.RemoveEmpty' => false,

    ],
    'generalFields' => [
        'HTML.Doctype' => 'HTML 4.01 Transitional',
        'HTML.Allowed' => '',
        'AutoFormat.AutoParagraph' => false,
        'AutoFormat.Linkify' => false,
    ],
];
