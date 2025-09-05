# PDF Metadata Blanker

A PHP script that monitors a folder for PDF files and automatically removes their metadata, including title, author, subject, keywords, creator, and producer information.

## Features

- **Folder Monitoring**: Automatically processes new PDF files dropped into a watch folder
- **Metadata Removal**: Completely removes all metadata from PDF files
- **File Organization**: Moves processed files to a separate folder and saves clean versions
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

3. **The script will automatically create these folders**:
   - `input_pdfs/` - Drop PDF files here


## Usage

### Continuous Monitoring Mode
Start the script to continuously monitor the input folder:
```bash
php PDF-blanker.php
```

The script will:
- Process any existing PDF files in the input folder
- Continue running and check for new files every 5 seconds
- Automatically process new PDFs as they're added

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

1. **File Detection**: The script monitors the `input_pdfs/` folder for PDF files
2. **Metadata Removal**: Uses FPDI and TCPDF libraries to create a clean copy without metadata
3. **File Organization**: 
   - Original file is moved to `input_pdfs/processed/`
   - Clean version is saved to `output_pdfs/`
4. **Logging**: All operations are logged to `pdf_blanker.log`

## Configuration

Edit `config.php` to customize:
- Input and output folder paths
- Check interval for monitoring
- Supported file extensions
- Metadata removal settings

## Folder Structure

```
project-folder/
├── PDF-blanker.php          # Main script
├── config.php               # Configuration file
├── composer.json            # Dependencies
├── README.md               # This file
├── pdf_blanker.log         # Log file (created automatically)
├── input_pdfs/             # Drop PDFs here
│   └── processed/          # Original files moved here
├── output_pdfs/            # Clean PDFs saved here
└── vendor/                 # Composer dependencies
```

## Example Workflow

1. **Start the script**:
   ```bash
   php PDF-blanker.php
   ```

2. **Drop a PDF file** into the `input_pdfs/` folder

3. **The script will**:
   - Detect the new file
   - Remove all metadata
   - Save the clean version to `output_pdfs/`
   - Move the original to `input_pdfs/processed/`
   - Log all operations

## Metadata Removed

The script removes these PDF metadata fields:
- Title
- Author
- Subject
- Keywords
- Creator
- Producer
- Creation date information
- Modification date information

## Logging

All operations are logged to `pdf_blanker.log` with timestamps:
- File processing status
- Errors and exceptions
- Directory creation
- File movements

## Troubleshooting

### Common Issues

1. **Permission Errors**: Ensure PHP has read/write access to the script directory
2. **Missing Dependencies**: Run `composer install` to install required libraries
3. **Memory Issues**: Large PDF files may require increasing PHP memory limit

### Log File
Check `pdf_blanker.log` for detailed error messages and processing information.

## Dependencies

- **FPDI**: For reading and importing existing PDF pages
- **TCPDF**: For creating new PDF files without metadata

## License

MIT License - Feel free to use and modify as needed.

## Support

For issues or questions, check the log file first for error details. The script provides comprehensive logging to help diagnose any problems.
