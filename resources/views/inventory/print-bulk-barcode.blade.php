<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Test Label</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
}

.label {
    width: 38mm;
    height: 25mm;
    position: relative;
    page-break-after: always;
}

.text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 14px;
    font-weight: bold;
    text-align: center;
}

@media print {
    @page {
        size: 38mm 25mm;
        margin: 0;
    }
}
</style>
</head>

<body>

<div class="label">
    <div class="text">TEST</div>
</div>

<script>
window.onload = function () {
    window.print();
};
</script>

</body>
</html>