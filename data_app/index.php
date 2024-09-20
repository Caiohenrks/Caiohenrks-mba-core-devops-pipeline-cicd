<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>QR Code Generator</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h1 class="card-title text-center">QR Code Generator</h1>
                        <form method="POST">
                            <div class="form-group">
                                <label for="text">Enter text to generate QR Code:</label>
                                <input type="text" id="text" name="text" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Generate QR Code</button>
                        </form>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['text'])) {
                            $text = $_POST['text'];
                            $data = json_encode(['text' => $text]);

                            $ch = curl_init('http://app:3000/generate');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                            $response = curl_exec($ch);
                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            if ($response === false) {
                                echo "<div class='alert alert-danger mt-3'>Error generating QR Code</div>";
                            } else {
                                if ($httpcode != 200) {
                                    echo "<div class='alert alert-danger mt-3'>Error: HTTP status code $httpcode</div>";
                                } else {
                                    $responseData = json_decode($response, true);
                                    if (isset($responseData['key'])) {
                                        $key = $responseData['key'];

                                        $ch = curl_init("http://app:3000/retrieve/$key");
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                        $qrResponse = curl_exec($ch);
                                        $qrHttpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                        curl_close($ch);

                                        if ($qrResponse === false) {
                                            echo "<div class='alert alert-danger mt-3'>Error retrieving QR Code</div>";
                                        } else {
                                            if ($qrHttpcode != 200) {
                                                echo "<div class='alert alert-danger mt-3'>Error: HTTP status code $qrHttpcode</div>";
                                            } else {
                                                $qrData = json_decode($qrResponse, true);
                                                if (isset($qrData['image'])) {
                                                    $imageData = $qrData['image'];
                                                    echo "<div class='text-center mt-3'><h2>Generated QR Code:</h2>";
                                                    echo "<img src='data:image/png;base64,{$imageData}' alt='QR Code' class='img-fluid mt-3'></div>";
                                                    echo "<div class='text-center mt-3'><p>Thank you for using, donate and help me host:</p>";
                                                    echo "<a href='https://www.paypal.com/donate/?business=GFURHWQ72A6PU&no_recurring=0&item_name=Thank+you+for+using%2C+donate+and+help+me+host&currency_code=USD' class='btn btn-success mt-2'>Donate</a></div>";
                                                } else {
                                                    echo "<div class='alert alert-danger mt-3'>Error retrieving QR Code data</div>";
                                                }
                                            }
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger mt-3'>Error generating QR Code</div>";
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXlG0/5Qx+5OBW8kLIOoUAutuCO+8r1zWsPkwlfKVD4roM9pAB/XtSrAzRj/" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgE+8kOnyZlXtra5V9i3mgBsiPw5T3jeBS6QEN/tz8ODf0ibcCd" crossorigin="anonymous"></script>
</body>
</html>
