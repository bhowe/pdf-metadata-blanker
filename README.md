# PDF Title Remover

A PHP script that monitors a folder for PDF files and automatically removes **only the title metadata** while preserving all other metadata (author, subject, keywords, creator, producer). Files are processed in place without moving or creating separate copies.

## Features

- **Folder Monitoring**: Automatically processes new PDF files dropped into a watch folder
- **Selective Metadata Removal**: Removes only the title metadata, preserving all other metadata
- **Recursive Processing**: Processes PDF files in subdirectories as well
- **In-Place Processing**: Files are processed directly without creating copies or moving files
- **Logging**: Comprehensive logging of all operations
- **Flexible Configuration**: Easy to customize folder paths and settings
- **Two Modes**: Continuous monitoring or one-time batch processing

## Requirements

- PHP 7.4 or higher
- Composer (for dependency management)

## Installation

1. **Clone or download the script files**
2. **Install dependencies using Composer**:
   ```bash
   composer install
   ```

3. **The script will automatically create the input folder**:
   - `input_pdfs/` - Drop PDF files here (including subdirectories)

## Usage

### Continuous Monitoring Mode

Start the script to continuously monitor the input folder:
```bash
php PDF-blanker.php
```

The script will:
- Process any existing PDF files in the input folder and subdirectories
- Continue running and check for new files every 5 seconds
- Automatically process new PDFs as they're added
- Process files in place (no file moving or copying)

### One-Time Processing Mode

Process existing files once and exit:
```bash
php PDF-blanker.php --once
```

### Using Composer Scripts

```bash
# Start continuous monitoring
composer run start

# Run one-time processing
composer run process-once
```

## How It Works

1. **File Detection**: The script recursively monitors the `input_pdfs/` folder and all subdirectories for PDF files
2. **Title Removal**: Uses FPDI and TCPDF libraries to create a new PDF with blank title metadata
3. **In-Place Processing**: 
   - Creates a temporary file during processing
   - Replaces the original file with the processed version
   - No files are moved or copied to other locations
4. **Logging**: All operations are logged to `pdf_blanker.log`

## Configuration

Edit `config.php` to customize:
- Input folder path (`watch_folder`)
- Check interval for monitoring (`check_interval`)
- Supported file extensions (`supported_extensions`)
- PDF processing settings including metadata preservation options

## Folder Structure

```
project-folder/
├── PDF-blanker.php          # Main script
├── config.php               # Configuration file
├── composer.json            # Dependencies
├── README.md               # This file
├── pdf_blanker.log         # Log file (created automatically)
├── input_pdfs/             # Drop PDFs here (processed in place)
│   ├── subfolder1/         # Subdirectories are processed recursively
│   └── subfolder2/
└── vendor/                 # Composer dependencies
```

## Example Workflow

1. **Start the script**:
   ```bash
   php PDF-blanker.php
   ```

2. **Drop a PDF file** into the `input_pdfs/` folder (or any subfolder)

3. **The script will**:
   - Detect the new file
   - Remove only the title metadata
   - Replace the original file with the processed version (in place)
   - Log all operations

## Metadata Processing

The script **removes only these PDF metadata fields**:
- **Title** ✅ (removed/blanked)

The script **preserves these PDF metadata fields**:
- **Author** ✅ (preserved)
- **Subject** ✅ (preserved)
- **Keywords** ✅ (preserved)
- **Creator** ✅ (preserved)
- **Producer** ✅ (preserved)
- Creation and modification dates (preserved)

## Logging

All operations are logged to `pdf_blanker.log` with timestamps:
- File processing status with relative paths
- Errors and exceptions
- Directory creation
- Processing statistics

## Troubleshooting

### Common Issues

1. **Permission Errors**: Ensure PHP has read/write access to the script directory and PDF files
2. **Missing Dependencies**: Run `composer install` to install required libraries
3. **Memory Issues**: Large PDF files may require increasing PHP memory limit
4. **File Lock Issues**: Ensure PDF files are not open in other applications during processing

### Log File

Check `pdf_blanker.log` for detailed error messages and processing information. The log shows relative paths for better readability.

## Dependencies

- **FPDI**: For reading and importing existing PDF pages
- **TCPDF**: For creating new PDF files with modified metadata
- **FPDF**: Base PDF library (dependency of FPDI)

## Configuration Options

The `config.php` file allows you to customize:

```php
return [
    'watch_folder' => __DIR__ . '/input_pdfs',    // Folder to monitor
    'check_interval' => 5,                         // Check frequency (seconds)
    'supported_extensions' => ['pdf'],             // File types to process
    'pdf_settings' => [
        'remove_title_only' => true,               // Only remove title
        'preserve_metadata' => [                   // Keep other metadata
            'author' => true,
            'subject' => true,
            'keywords' => true,
            'creator' => true,
            'producer' => true
        ]
    ]
];
```

## License

MIT License - Feel free to use and modify as needed.

## Support

For issues or questions, check the log file first for error details. The script provides comprehensive logging to help diagnose any problems.

## Version History

- **v1.1**: Current version - Title-only removal with metadata preservation
- **v1.0**: Initial release