<?php
// Function to safely output file for download
function outputFile($filePath, $fileName) {
    if (file_exists($filePath)) {
        // Get the file extension
        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
        
        // Set appropriate content type based on extension
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4'
        ];
        
        $contentType = isset($contentTypes[$fileExt]) ? $contentTypes[$fileExt] : 'application/octet-stream';
        
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clear output buffer and read file
        ob_clean();
        flush();
        readfile($filePath);
        exit;
    } else {
        return false;
    }
}

// Check if file parameter exists
if (isset($_GET['file'])) {
    $fileCode = $_GET['file'];
    
    // Sanitize the input (only allow alphanumeric characters)
    if (!preg_match('/^[a-zA-Z0-9]{6}$/', $fileCode)) {
        die("Invalid file code");
    }
    
    // Find the file in the uploads directory
    $uploadDir = 'uploads/';
    $files = scandir($uploadDir);
    
    foreach ($files as $file) {
        // Skip directory entries
        if ($file == '.' || $file == '..') {
            continue;
        }
        
        // Check if file starts with our code
        $currentFileCode = pathinfo($file, PATHINFO_FILENAME);
        
        if ($currentFileCode === $fileCode) {
            // Get original filename or use the code as filename
            $fileName = $file;
            
            // Output the file
            if (!outputFile($uploadDir . $file, $fileName)) {
                echo "File not found";
            }
            exit;
        }
    }
    
    // If we get here, file was not found
    echo "File not found";
} else {
    echo "No file specified";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .error {
            color: red;
            margin: 30px 0;
        }
        .home-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error">
        <!-- Error message will appear here if the file isn't found -->
    </div>
    
    <div class="home-link">
        <a href="index.php">Return to Home Page</a>
    </div>
</body>
</html>