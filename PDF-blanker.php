<?php
declare(strict_types=1);

/**
 * PDF Title Remover
 * 
 * This script monitors a specified folder for PDF files and removes only the title metadata,
 * while preserving all other metadata (author, subject, keywords, creator, producer).
 * 
 * Requirements:
 * - PHP 7.4+
 * - FPDI library (install via Composer: composer require setasign/fpdi)
 * - TCPDF library (install via Composer: composer require tecnickcom/tcpdf)
 * 
 * @author Blake Howe
 * @version 1.1
 */

require_once __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;

class PDFTitleRemover
{
    private string $watchFolder;
    private array $supportedExtensions = ['pdf'];
    private string $logFile;
    
    public function __construct(?string $watchFolder = null)
    {
        $this->watchFolder = $watchFolder ?? __DIR__ . '/input_pdfs';
        $this->logFile = __DIR__ . '/pdf_blanker.log';
        
        $this->createDirectories();
    }
    
    /**
     * Create necessary directories if they don't exist
     */
    private function createDirectories(): void
    {
        if (!is_dir($this->watchFolder)) {
            if (!mkdir($this->watchFolder, 0755, true)) {
                $this->log("ERROR: Failed to create directory: {$this->watchFolder}");
                throw new Exception("Failed to create directory: {$this->watchFolder}");
            }
            $this->log("Created directory: {$this->watchFolder}");
        }
    }
    
    /**
     * Start monitoring the folder for new PDF files
     */
    public function startWatching(): void
    {
        $this->log("Starting PDF Title Remover...");
        $this->log("Processing folder recursively: {$this->watchFolder}");
        $this->log("Files will be processed in place (no moving)");
        
        // Process existing files first
        $this->processExistingFiles();
        
        // Start continuous monitoring (intentional infinite loop)
        $this->log("Starting continuous monitoring...");
        // phpcs:ignore Generic.CodeAnalysis.UnconditionalIfStatement.Found
        while (true) {
            $this->checkForNewFiles();
            sleep(5); // Check every 5 seconds
        }
    }
    
    /**
     * Process any existing PDF files in the watch folder
     */
    private function processExistingFiles(): void
    {
        $files = $this->getPDFFiles();
        if (!empty($files)) {
            $this->log("Found " . count($files) . " existing PDF files to process");
            foreach ($files as $file) {
                $this->processPDFFile($file);
            }
        }
    }
    
    /**
     * Check for new PDF files and process them
     */
    private function checkForNewFiles(): void
    {
        $files = $this->getPDFFiles();
        
        foreach ($files as $file) {
            $this->processPDFFile($file);
        }
    }
    
    /**
     * Get all PDF files in the watch folder recursively
     */
    private function getPDFFiles(): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->watchFolder, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }
            
            $extension = strtolower($fileInfo->getExtension());
            if (in_array($extension, $this->supportedExtensions)) {
                $files[] = $fileInfo->getPathname();
            }
        }
        
        return $files;
    }
    
    /**
     * Process a single PDF file to remove title metadata in place
     */
    private function processPDFFile(string $filePath): void
    {
        try {
            $fileName = basename($filePath);
            $relativePath = str_replace($this->watchFolder . '/', '', $filePath);
            $this->log("Processing file: {$relativePath}");
            
            // Create temporary output filename
            $tempPath = $filePath . '.tmp';
            
            // Remove title metadata using FPDI/TCPDF
            $this->removeTitleMetadata($filePath, $tempPath);
            
            // Replace original file with processed version
            if (rename($tempPath, $filePath)) {
                $this->log("Successfully processed: {$relativePath}");
            } else {
                $this->log("ERROR: Failed to replace original file: {$relativePath}");
                // Clean up temp file if replacement failed
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }
            
        } catch (Exception $e) {
            $this->log("ERROR processing {$filePath}: " . $e->getMessage());
            // Clean up temp file if processing failed
            $tempPath = $filePath . '.tmp';
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
    
    /**
     * Remove title metadata from PDF using FPDI and TCPDF
     */
    private function removeTitleMetadata(string $inputPath, string $outputPath): void
    {
        // Create new PDF instance
        $pdf = new Fpdi();
        
        // Get page count
        $pageCount = $pdf->setSourceFile($inputPath);
        
        // Import each page
        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            // Import page
            $templateId = $pdf->importPage($pageNum);
            $size = $pdf->getTemplateSize($templateId);
            
            // Add page with same dimensions
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            
            // Use the imported page
            $pdf->useTemplate($templateId);
        }
        
        // Set blank metadata (only title)
        $pdf->SetTitle('');
        // Keep other metadata intact
        // $pdf->SetAuthor('');
        // $pdf->SetSubject('');
        // $pdf->SetKeywords('');
        // $pdf->SetCreator('');
        // $pdf->SetProducer('');
        
        // Save the cleaned PDF
        $pdf->Output($outputPath, 'F');
    }
    
    /**
     * Log messages with timestamp
     */
    private function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$message}" . PHP_EOL;
        
        // Output to console
        echo $logEntry;
        
        // Write to log file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Run the PDF title remover as a one-time process (no continuous monitoring)
     */
    public function processOnce(): void
    {
        $this->log("Running PDF Title Remover (one-time process)...");
        $this->processExistingFiles();
        $this->log("One-time processing complete.");
    }
}

// Load configuration
$config = require_once __DIR__ . '/config.php';

// Check command line arguments
$runOnce = false;
if (isset($argv[1]) && $argv[1] === '--once') {
    $runOnce = true;
}

try {
    $blanker = new PDFTitleRemover($config['watch_folder']);
    
    if ($runOnce) {
        $blanker->processOnce();
    } else {
        $blanker->startWatching();
    }
    
} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
