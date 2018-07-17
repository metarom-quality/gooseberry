<html>
  <head>
    <title>Production Sheet Generator</title>
    <link rel='stylesheet' type='text/css' href='./styles.css'>
  </head>
  <body>
    <small>
      <div class='navbar'>
        <ul>
          <li><a href='index.php'>Home</a><b> &nbsp; | &nbsp; </b></li>
          <li>Production Sheet<b> &nbsp; | &nbsp; </b></li>
          <li>Product Information Form</li>
        </ul>
      </div>
      <p class='clear'>
    </small>
    <h1>Production Sheet Information</h1>
    <div class='form'>
      <form action='PS_result.php' method='post'>
        <p>Product Code: <input type='text' name='ref' /></p>
        <p>Product Quantity: <input type='text' name='quant' /></p>
        <p>Product Batch: <input type='text' name='batch' /></p>
        <p><input type='submit' /></p>
      </form>
    </div>
  </body>
</html>
