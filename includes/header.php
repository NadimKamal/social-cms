<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social CMS</title>
    <script src="<?= APP_URL ?>assets/js/tailwind.min.js"></script>
    <script src="<?= APP_URL ?>assets/js/helper.js"></script>
    <script>
        const APP_URL = "<?= APP_URL ?>";
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb'
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-100">
    
  <?php include 'navbar.php'; ?>

    <main class="flex-1">