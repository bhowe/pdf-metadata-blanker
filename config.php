<?php
declare(strict_types=1);

/**
 * PDF Title Remover Configuration
 * 
 * Modify these settings to customize the behavior of the PDF title remover script.
 */

return [
    // Folder to watch for new PDF files
    'watch_folder' => __DIR__ . '/input_pdfs',
    
    // Folder where cleaned PDFs will be saved
    'output_folder' => __DIR__ . '/output_pdfs',
    
    // How often to check for new files (in seconds)
    'check_interval' => 5,
    
    // Supported file extensions (lowercase)
    'supported_extensions' => ['pdf'],
    
    // Log file location
    'log_file' => __DIR__ . '/pdf_blanker.log',
    
    // Whether to create processed subfolder
    'create_processed_folder' => true,
    
    // PDF processing settings
    'pdf_settings' => [
        'version' => '1.4',
        'remove_title_only' => true,
        'preserve_metadata' => [
            'author' => true,
            'subject' => true,
            'keywords' => true,
            'creator' => true,
            'producer' => true
        ]
    ]
];
