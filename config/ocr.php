<?php

return [
    'enabled' => env('OCR_ENABLED', true),
    'language' => env('OCR_LANGUAGE', 'ind'),
    'dpi' => (int) env('OCR_DPI', 300),
    'max_pages' => (int) env('OCR_MAX_PAGES', 200),
    'tesseract_binary' => env('OCR_BINARY', 'tesseract'),
    'pdf_to_image_binary' => env('PDF_TO_IMAGE_BINARY', 'pdftoppm'),
    'pdftotext_binary' => env('PDF_TO_TEXT_BINARY', 'pdftotext'),
    'temp_path' => storage_path('app/ocr-temp'),
];
