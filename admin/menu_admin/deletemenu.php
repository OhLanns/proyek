<?php
include "../../config.php";
// Check if ID parameter exists
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    // First, get the image filename to delete it from server
    $sql_select = "SELECT img FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_file = $row['img'];
        
        // Delete the menu item from database
        $sql_delete = "DELETE FROM menu WHERE id = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()) {
            // Delete the associated image file if it exists
            if(!empty($image_file)){
                $image_path = "../../gambar/menu/" . $image_file;
                if(file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Redirect back with success message
            header("Location: readmenu.php?status=deleted");
            exit();
        } else {
            // Redirect back with error message
            header("Location: readmenu.php?status=delete_error");
            exit();
        }
    } else {
        // Menu item not found
        header("Location: readmenu.php?status=not_found");
        exit();
    }
} else {
    // No ID provided
    header("Location: readmenu.php?status=invalid_id");
    exit();
}

$conn->close();
?>