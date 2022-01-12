<?php
function crypto_chain()
{
?>

  <html>

  <head>

    <meta charset="utf-8">
    <title></title>
  

    

    <style type="text/css">
      .exportbutt {
        border-width: 0px;
        margin-top: 10px;
        margin-bottom: 20px;
        padding: 10px 18px;
        background: #FFFFFF;
        border: 1px solid #C4C4C4;
        box-sizing: border-box;
        border-radius: 6px;
        font-family: Open Sans;
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 19px;
        display: flex;
        align-items: center;
        text-align: center;
        text-transform: capitalize;
        color: #888888;
      }

      tr>th {
        font-family: Open Sans;
        font-style: normal;
        font-weight: 600;
        font-size: 15px;
        line-height: 20px;
        text-align: center;
        vertical-align: top !important;
        letter-spacing: 0.01em;
      }

      tr>td {
        font-family: Open Sans;
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 19px;
        text-align: center;
        letter-spacing: 0.01em;
      }

      .headh3 {
        font-family: Open Sans;
        font-style: normal;
        font-weight: 600;
        font-size: 24px;
        line-height: 33px;
        letter-spacing: 1px;
      }

      .container {
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 115, 175, 0.25);
      }
    </style>


    <script>
      jQuery(document).ready(function() {

        var table = jQuery('#example').DataTable();
        table
          .order([0, 'desc'])
          .draw();
      });
    </script>

    <script type="text/javascript">
      function download_table_as_csv(table_id, separator = ',') {

        var rows = document.querySelectorAll('table#' + table_id + ' tr');

        var csv = [];
        for (var i = 0; i < rows.length; i++) {
          var row = [],
            cols = rows[i].querySelectorAll('td, th');
          for (var j = 0; j < cols.length; j++) {

            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ')

            data = data.replace(/"/g, '""');

            row.push('"' + data + '"');
          }
          csv.push(row.join(separator));
        }
        var csv_string = csv.join('\n');

        var filename = 'Transaction_History ' + '_' + new Date().toLocaleDateString() + '.csv';
        var link = document.createElement('a');
        link.style.display = 'none';
        link.setAttribute('target', '_blank');
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    </script>

  </head>

  <body style="background-color:#E5E5E5;">
    <h3 style=" margin-top: 30px;margin-left: 10px;" class="headh3">Transaction History </h3>
    <div class="container">

      <div class="d-flex flex-row-reverse" style="align-items: center;">
        <button onclick="download_table_as_csv('example');" class="exportbutt">&#10515; Export CSV</button>
      </div>


      <table id="example" class="table table-striped table-bordered table-sm" style="word-break: break-all;">
        <thead style="background-color:#1888AF;">
          <tr>
            <th class="th-sm" style="color:#ffffff;">Date & Time

            </th>
            <th class="th-sm" style="color:#ffffff;">Trans ID
            </th>
            <th class="th-sm" style="color:#ffffff;">User Name

            </th>

            <th class="th-sm" style="color:#ffffff;">Product Name
            </th>
            <th class="th-sm" style="color:#ffffff;">Sender Address

            </th>
            <th class="th-sm" style="color:#ffffff;">Recipient Address

            </th>
            <th class="th-sm" style="color:#ffffff;">ETH Value

            </th>
            <th class="th-sm" style="color:#ffffff;">ETH Fees

            </th>
            <th class="th-sm" style="color:#ffffff;">$ Value

            </th>
            <th class="th-sm" style="color:#ffffff;">Status

            </th>
          </tr>
        </thead>
        <tbody>

          <?php
          global $wpdb, $table_prefix;
          $table_name = $table_prefix . 'transactions';

          $data = $wpdb->get_results("SELECT * FROM $table_name");
          $data1 = $wpdb->get_results("SELECT trans_id,order_id FROM $table_name where status = 'pending'");

          foreach ($data1 as $dat1) {

            $ord = $dat1->order_id;

            $wpdb->update(
              'wp_posts',
              array(
                'post_status' => 'wc-completed'
              ),
              array('ID' => $ord)
            );
          }

          foreach ($data as $dat) {

          ?>
            <tr>
              <td style="width:80px;"><?php echo esc_html($dat->date_time); ?></td>
              <td><?php echo esc_html($dat->trans_id); ?></td>
              <td style="width:100px;"><?php echo esc_html($dat->user_name); ?></td>

              <td style="width:80px;"> <?php echo esc_html($dat->product_description); ?></td>
              <td><?php echo esc_html($dat->sender_address); ?></td>
              <td><?php echo esc_html($dat->receiver_address); ?></td>
              <td style="width:55px;"><?php echo esc_html($dat->eth_value); ?></td>
              <td style="width:50px;"><?php echo esc_html($dat->eth_fees); ?></td>
              <td style="width:70px;"><?php echo esc_html($dat->doller_value); ?></td>
              <td style="width:80px;"><?php echo esc_html($dat->status);
                                      if ($dat->status == 'pending') { ?>
                  <img src="<?php echo plugin_dir_url(__FILE__) . 'image/loader.png'; ?>" style="width: 40px;" onClick="window.location.reload();" id="loadimg">
                <?php  } ?>
              </td>
            </tr>

          <?php

          }



          ?>


        </tbody>

      </table>
    </div>

    <script>
      // Access the array elements
      var passedArray = <?php echo json_encode($data1); ?>;


      async function getstatus(var1) {
        let x = await ethereum.request({
          method: 'eth_getTransactionReceipt',
          params: [var1],
        })
        //console.log(x+' status'); 
        return x;
      }

      for (var i = 0; i < passedArray.length; i++) {

        getstatus(passedArray[i].trans_id).then(res => {

          txh = res.transactionHash;
          txstatus = res.status;

          Stat = '';
          if (txstatus == '0x1') {
            Stat = 'Completed';
          } else if (txstatus == '0x0') {
            Stat = 'Failed';
          } else {
            Stat = 'Pending';
          }


          updatestatus(txh, Stat);


        });

      }

      function updatestatus(hash, status) {

        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            "action": "cryptupdatedata",
            "transactionhash": hash,
            "txstatus": status
          },
          success: function(data) {
            console.log('done');
          }
        });

      }
    </script>



  </body>

  </html>

<?php
}