<html>
  <head>
    <title>
      Production Sheet of <?php echo htmlspecialchars($_POST['ref']); ?>
    </title>
    <link rel=stylesheet href='styles.css' type='text/css'>
  </head>
  <body>
    <small>
      <div class='navbar'>
        <ul>
          <li><a href='index.php'>Home</a><b> &nbsp; | &nbsp; </b></li>
          <li><a href='PS.php'>Production Sheet</a><b> &nbsp; | &nbsp; </b></li>
          <li>Product Information Form</li>
        </ul>
      </div>
    </small>
    <p class='clear'></p>
    <h1>Production Sheet of <?php echo htmlspecialchars($_POST['ref']); ?></h1>
    <div class='PS_form'>
      <ul class='summary'>
        <li>Batch #: <?php echo $_POST['batch']; ?> </li>
        <li>Production: <?php echo date("d/m/Y"); ?></li>
        <li>Best Before: <?php echo date("d/m/Y", strtotime("+1 year")); ?></li>
      </ul>
      <p>Packaging: </p>
      <p>Comments: </p>
      <?php
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
        class Product extends SQLite3 {
          function __construct() {
            $this->open('/home/tomate/Warehouse/syte/meta.db');
          }
        }
        $db = new Product();
        if(!$db) {
          echo $db->lastErrorMsg();
        } else {
          echo "<table><tr><th class='ind'>INGREDIENT</th>"
            ."<th class='descr'>DESCRIPTION</th>"
            ."<th class='picto'>&nbsp;</th>"
            ."<th>FORMULA</th>"
            ."<th>QUANTITY</th>"
            ."<th>WEIGH</th>"
            ."<th>BATCH</th></tr>";
          $sql = "SELECT * FROM 'receipe' WHERE product LIKE '". $_POST['ref']. "'";
          $ret = $db->query($sql);
          while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
            if($row['ingredient']) {
              echo "<tr class='normal'><td class='ind'>". $row['ingredient'] . "</td>";
              echo "<td class='descr'>&nbsp;</td>";
              echo "<td class='picto'>&nbsp;</td>";
              echo "<td class='formu'>". number_format($row['quantity'], 3) . "</td>";
              echo "<td class='quant'>"
                .number_format($row['quantity'] * $_POST['quant'], 5). "</td>"
                ."<td class='misc'>&nbsp;</td><td class='misc'>&nbsp;</td></tr>";
            } else {
              echo "<tr class='inst'><td class='ind'>". $row['instruction'] . "</td>";
              echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='misc'>&nbsp;</td><td class='misc'>&nbsp;</td></tr>";
            }
          }
          $db->close();
          echo "<tr class='normal'><td class='ind'>TOTAL</td><td class='descr'>&nbsp;</td><td class='picto'>&nbsp;</td><td class='formu'>1.000</td><td class='quant'>"
            .number_format($_POST['quant'],5). "</td><td class='misc'>&nbsp;</td><td class='misc'>&nbsp;</td></tr>";
          echo "</table>";
        }
      ?>
      <div id='operator_check'>
        <small>
          <p><b><u>Operator to:</u></b></p>
          <table id='opcheck'>
            <tr><td class='opdis'>Check conformance of smell during production:</td>
                <td class='pf'>Pass / Fail</td>
                <td class='sig'>&nbsp;</td></tr>
            <tr><td class='opdis'>Take batch sample and record reference, batch number, and date</td>
                <td class='pf'>&nbsp;</td>
                <td class='sig'>&nbsp;</td></tr>
            <tr><td class='opdis'>Check CCP1 Filter for foreign objects at the end of filling</td>
                <td class='pf'>Pass / Fail</td>
                <td class='sig'>&nbsp;</td></tr>
          </table>
        </small>
      </div>
      <hr>
      <div id='qa_check'>
        <table id='qach'>
          <tr><th class='qc'>Tests</th>
              <th class='qv'>Value</th>
              <th class='qcc'>Control</th>
              <th class='qn'>Name</th>
              <th class='qsig'>Initials</th>
              <th class='qt'>Tolerance</th></tr>
          <tr><td class='qc'>Density</td>
              <td class='qv'>placeholder</td>
              <td class='qcc'>&nbsp;</td>
              <td class='qn'>&nbsp;</td>
              <td class='qsig'>&nbsp;</td>
              <td class='qt'>margin is: ±0.05</td></tr>
          <tr><td class='qc'>Brix</td>
              <td class='qv'>placeholder</td>
              <td class='qcc'>&nbsp;</td>
              <td class='qn'>&nbsp;</td>
              <td class='qsig'>&nbsp;</td>
              <td class='qt'>margin is: ±0.05</td></tr>
          <tr><td class='qc'>Aspect</td>
              <td class='qv'>placeholder</td>
              <td class='qcc'>&nbsp;</td>
              <td class='qn'>&nbsp;</td>
              <td class='qsig'>&nbsp;</td>
              <td class='qt'>Compare w. the last batch</td></tr>
          <tr><td class='qc'>Smell</td>
              <td class='qv'>placeholder</td>
              <td class='qcc'>&nbsp;</td>
              <td class='qn'>&nbsp;</td>
              <td class='qsig'>&nbsp;</td>
              <td class='qt'>Compare w. the last batch</td></tr>
          <tr><td class='qc'>Taste</td>
              <td class='qv'>placeholder</td>
              <td class='qcc'>&nbsp;</td>
              <td class='qn'>&nbsp;</td>
              <td class='qsig'>&nbsp;</td>
              <td class='qt'>Compare w. the last batch</td></tr>
        </table>
        <p>QC person validates confromance of batch:</p>
        <p>If fail, details of actions taken to correct:</p>
        <p class='car'>&nbsp;</p>
        <p class='carnr'>CAR #: <tag>&nbsp;</tag></p>
        <p class='clear'>
        <p>Released by: <tag>&nbsp;</tag> &#09;  Date:<tag>&nbsp;</tag></p>
        <p>Reviewed by: <tag>&nbsp;</tag> &#09;  Date:<tag>&nbsp;</tag></p>
      </div>
    </div>
  </body>
</html>
