<?php

return [

    'defines' => 'DOMPDF_ENABLE_REMOTE',
    
    'options' => [
        /**
         * The location of the DOMPDF font directory
         */
        "font_dir" => storage_path('fonts'),
        
        /**
         * The location of the DOMPDF font cache directory
         */
        "font_cache" => storage_path('fonts'),
        
        /**
         * The location of temporary directory.
         */
        "temp_dir" => sys_get_temp_dir(),
        
        /**
         * dompdf's "chroot"
         */
        "chroot" => realpath(base_path()),
        
        /**
         * Protocol whitelist
         */
        "allowed_protocols" => [
            "file://" => ["rules" => []],
            "http://" => ["rules" => []],
            "https://" => ["rules" => []]
        ],
        
        /**
         * @var string
         */
        "log_output_file" => null,
        
        /**
         * Whether to enable font subsetting or not.
         */
        "enable_font_subsetting" => false,
        
        /**
         * The PDF rendering backend to use
         */
        "pdf_backend" => "CPDF",
        
        /**
         * Default media type.
         */
        "default_media_type" => "screen",
        
        /**
         * Default paper size.
         */
        "default_paper_size" => "a4",
        
        /**
         * Default font family.
         */
        "default_font" => "serif",
        
        /**
         * DPI setting
         */
        "dpi" => 96,
        
        /**
         * Enable inline PHP
         */
        "enable_php" => false,
        
        /**
         * Enable inline Javascript
         */
        "enable_javascript" => true,
        
        /**
         * Enable remote file access
         */
        "enable_remote" => true,
        
        /**
         * A ratio applied to the fonts height to be more like browsers' line height
         */
        "font_height_ratio" => 1.1,
        
        /**
         * Use the HTML5 Lib parser
         */
        "enable_html5_parser" => true,
    ],
];
