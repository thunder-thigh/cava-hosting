<?php
// Start the session
session_start();

// Function to generate a random 6-character code
function generateFileCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Process the upload if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $uploadOk = 1;
    $targetDir = "uploads/";
    $originalName = basename($_FILES["fileToUpload"]["name"]);
    $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    // Generate unique 6-character code for the file
    $newFileName = generateFileCode();
    
    // Add extension if original file had one
    if ($fileType) {
        $newFileName .= "." . $fileType;
    }
    
    $targetFile = $targetDir . $newFileName;
    
    // Check if file already exists (very unlikely with random code)
    if (file_exists($targetFile)) {
        $_SESSION["message"] = "Error: File with this code already exists. Please try again.";
        $uploadOk = 0;
    }
    
    // Check file size (limit to 50MB)
    if ($_FILES["fileToUpload"]["size"] > 50000000) {
        $_SESSION["message"] = "Error: Your file is too large.";
        $uploadOk = 0;
    }
    
    // Try to upload file
    if ($uploadOk) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            // Construct the download URL
            $downloadUrl = "http://" . $_SERVER['HTTP_HOST'] . "/download.php?file=" . pathinfo($newFileName, PATHINFO_FILENAME);
            $_SESSION["message"] = "File has been uploaded successfully!";
            $_SESSION["downloadUrl"] = $downloadUrl;
        } else {
            $_SESSION["message"] = "Error: There was an error uploading your file.";
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local File Sharing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f5f5f5;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .url-box {
            background-color: #e0f7fa;
            padding: 10px;
            border-radius: 3px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <h1>Local File Sharing</h1>
    
    <div class="container">
        <h2>Upload a File</h2>
        
        <?php
        // Display message if set
        if (isset($_SESSION["message"])) {
            $messageClass = (strpos($_SESSION["message"], "Error") !== false) ? "error" : "success";
            echo "<div class='" . $messageClass . "'>" . $_SESSION["message"] . "</div>";
            
            // If upload was successful and we have a URL
            if (isset($_SESSION["downloadUrl"])) {
                echo "<p>Your file is now available at:</p>";
                echo "<div class='url-box'>" . $_SESSION["downloadUrl"] . "</div>";
                echo "<p>Share this link with anyone who needs to download the file.</p>";
                
                // Clear the URL from session
                unset($_SESSION["downloadUrl"]);
            }
            
            // Clear the message from session
            unset($_SESSION["message"]);
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <p>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
            </p>
            <p>
                <input type="submit" value="Upload File" name="submit">
            </p>
        </form>
    </div>
</body>
</html>