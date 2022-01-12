<?php
function crypt_setting()
{ ?>
    <html>

    <head>
        <meta charset="utf-8">
        <title></title>
       
        

        <style type="text/css">
            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            /* Hide default HTML checkbox */
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            /* The slider */
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked+.slider {
                background-color: #2196F3;
            }

            input:focus+.slider {
                box-shadow: 0 0 1px #2196F3;
            }

            input:checked+.slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }

            .btn {
                width: 110px;
                background: #0073AF;
                box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.25), 0px 4px 20px rgba(0, 0, 0, 0.25);
                border-radius: 30px;
                font-family: Open Sans;
                font-style: normal;
                font-weight: normal;
                font-size: 16px;
                line-height: 22px;
                letter-spacing: 0.01em;
                color: #FFFFFF;

            }

            .btn2 {
                padding: 8px 16px;
                width: auto;
                border-width: 0px;
                background: #0073AF;
                box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.25), 0px 4px 20px rgba(0, 0, 0, 0.25);
                border-radius: 30px;
                font-family: Open Sans;
                font-style: normal;
                font-weight: normal;
                font-size: 16px;
                line-height: 22px;
                letter-spacing: 0.01em;
                color: #FFFFFF;

            }

            .no {
                padding-right: 10px;
            }

            .yes {
                padding-left: 10px;
            }

            .table-borderless>tbody>tr>td,
            .table-borderless>tbody>tr>th,
            .table-borderless>tfoot>tr>td,
            .table-borderless>tfoot>tr>th,
            .table-borderless>thead>tr>td,
            .table-borderless>thead>tr>th {
                border: none;
            }

            .metabut {
                background: #52B540;
                border-radius: 30px;
                color: #ffffff;
            }

            #walad {
                background: #EEEEEE;
                mix-blend-mode: normal;
                border: 0.5px solid #C4C4C4;
                border-radius: 3.08372px;
            }

            .wadd {
                font-family: Open Sans;
                font-style: normal;
                font-weight: normal;
                font-size: 22px;
                line-height: 30px;
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
        </style>
    </head>


    <body style="background-color:#E5E5E5;">
        <h3 style="margin-top: 30px;margin-left: 10px;" class="headh3">Crypto Wallet Setting</h3>
        <div class="container">
            <table class="table table-borderless" style="margin-top: 50px;background: #FFFFFF;
    box-shadow: 0px 4px 4px rgba(0, 115, 175, 0.25);padding:50px 50px; shape-margin: 12p;">


                <tbody>
                    <?php
                    global $wpdb, $table_prefix;
                   
                    $table_name = $table_prefix . 'cryptochain';

                    $data = $wpdb->get_results("SELECT * FROM $table_name");

                    //echo $data[0]->id;
                    $place = '';
                    if (isset($data[0]->wallet_address)) {
                        $place = $data[0]->wallet_address;
                    } else {
                        $place = "Autofill Wallet Address";
                    }
                    ?>

                    <form name="frm" method="post">


                        <tr>
                            <td style="text-align:center; padding-top: 60px; width:40%;" class="wadd">Wallet Address</td>
                            <td style="padding-top: 60px;"><input type="text" id="accounts" placeholder="<?php echo $place; ?>" style="width: 350px;" name="waladd" readonly>

                                <br>
                                <span id="valid" style="color:green;"></span>
                                <input type="hidden" name="network" id="network">
                                <input type="hidden" name="chainId" id="chainId">
                                <input type="hidden" name="upid" value="<?php echo esc_attr($data[0]->id); ?>">
                            </td>
                            <td style="padding-top: 60px;"> <span id="connectButton" class="btn2">Install MetaMask</span> <br>
                            </td>

                        </tr>
                        <tr>
                            <td style="text-align:center;" class="wadd">Enable Payment</td>
                            <td> <span class="no">No</span><label class="switch">
                                    <?php
                                    $checked = '';
                                    if (isset($data[0]->display_status)) {
                                        $checked = $data[0]->display_status;
                                    }else{
                                        $checked = '';
                                    }
                                    
                                    if ($checked == 'on') {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }

                                    ?>
                                    <input type="checkbox" name="check" <?php echo esc_attr($checked); ?>>
                                    <span class="slider round"></span>
                                </label><span class="yes">Yes </span></td>
                            <td>To update the wallet address please connect to that wallet via Metamask.</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;" class="wadd">Mode</td>
                            <td> <span class="no">Test</span><label class="switch">
                                    <?php
                                    $checked1 = '';
                                    if (isset($data[0]->mode)) {
                                        $checked1 = $data[0]->mode;
                                    }else{
                                        $checked1 = '';
                                    }
                                    
                                    if ($checked1 == 'on') {
                                        $checked1 = "checked";
                                    } else {
                                        $checked1 = "";
                                    }

                                    ?>
                                    <input type="checkbox" name="mode" <?php echo esc_attr($checked1); ?>>
                                    <span class="slider round"></span>
                                </label><span class="yes">Live</span></td>

                        </tr>



                        <tr>
                            <td></td>
                            <td style="padding-bottom: 60px;"><input type="submit" value="Save" class="btn" name="ins"></td>
                        </tr>
                    </form>


                </tbody>
            </table>
        </div>


        <script>
            const c = document.getElementById("accounts").placeholder;

            const networkDiv = document.getElementById('network');
            const chainIdDiv = document.getElementById('chainId');
            const accountsDiv = document.getElementById('accounts');
            const validtext = document.getElementById('valid');
            // Basic Actions Section
            const onboardButton = document.getElementById('connectButton');

            const isMetaMaskInstalled = () => {
                //Have to check the ethereum binding on the window object to see if it's installed
                const {
                    ethereum
                } = window;
                return Boolean(ethereum && ethereum.isMetaMask);
            };
            console.log('metamask installed' + isMetaMaskInstalled());
            const initialize = async () => {
                let onboarding


                let accounts
                //l/et piggybankContract
                let accountButtonsInitialized = false
                const accountButtons = []
                const isMetaMaskConnected = () => accounts && accounts.length > 0
                const onClickInstall = () => {
                    onboardButton.innerText = 'Install Metamask (Please refresh)'
                    onboardButton.disabled = true
                    if (navigator.userAgent.indexOf('Chrome') > -1) {

                        window.open('https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn', '_blank');

                    } else if (navigator.userAgent.indexOf("Firefox") > -1) {
                        window.open('https://addons.mozilla.org/en-US/firefox/addon/ether-metamask/', '_blank');

                    } else if ('/Edge\/\d./i'.test(navigator.userAgent) > -1 ||
                        navigator.userAgent.indexOf("rv:") > -1) {
                        window.open('https://microsoftedge.microsoft.com/addons/detail/metamask/ejbalbakoplchlghecdalmeeeajnimhm?hl=en-US', '_blank');
                    } else {
                        alert('Your current browser is not supported for metamask payment use Chrome,firefox or edge');
                    }
                    onboarding.startOnboarding()
                }
                const onClickConnect = async () => {
                    try {
                        const newAccounts = await ethereum.request({
                            method: 'eth_requestAccounts',
                        })
                        handleNewAccounts(newAccounts)
                    } catch (error) {
                        console.error(error)
                    }
                }
                const updateButtons = () => {
                    const accountButtonsDisabled = !isMetaMaskInstalled() || !isMetaMaskConnected()
                    if (accountButtonsDisabled) {
                        for (const button of accountButtons) {
                            // button.disabled = true
                        }
                        //  clearTextDisplays()
                    } else {
                        // deployButton.disabled = false
                        // sendButton.disabled = false

                    }

                    if (!isMetaMaskInstalled()) {
                        onboardButton.innerText = 'Click here to install MetaMask!'
                        onboardButton.onclick = onClickInstall
                        onboardButton.disabled = false
                    } else if (isMetaMaskConnected()) {
                        onboardButton.innerText = 'Metamask Connected'
                        onboardButton.disabled = true
                        onboardButton.style.backgroundColor = '#52B540'
                        validtext.innerHTML = 'Valid Address'
                        getAccount();
                        if (onboarding) {
                            onboarding.stopOnboarding()
                        }
                    } else if (!isMetaMaskConnected() && c !== 'Autofill Wallet Address') {
                        onboardButton.innerText = 'Connect to Update'
                        onboardButton.onclick = onClickConnect
                        onboardButton.disabled = false
                    } else {
                        onboardButton.innerText = 'Connect metamask account'
                        onboardButton.onclick = onClickConnect
                        onboardButton.disabled = false
                    }
                }

                const initializeAccountButtons = () => {

                    if (accountButtonsInitialized) {
                        return
                    }
                    accountButtonsInitialized = true

                }

                function handleNewAccounts(newAccounts) {
                    accounts = newAccounts
                    accountsDiv.value = accounts

                    if (isMetaMaskConnected()) {
                        initializeAccountButtons()
                    }
                    updateButtons()
                }

                function handleNewChain(chainId) {
                    chainIdDiv.value = chainId
                }

                function handleNewNetwork(networkId) {
                    networkDiv.value = networkId
                }

                async function getNetworkAndChainId() {
                    try {
                        const chainId = await ethereum.request({
                            method: 'eth_chainId',
                        })
                        handleNewChain(chainId)

                        const networkId = await ethereum.request({
                            method: 'net_version',
                        })
                        handleNewNetwork(networkId)
                    } catch (err) {
                        console.error(err)
                    }
                }
                updateButtons()
                if (isMetaMaskInstalled()) {
                    ethereum.autoRefreshOnNetworkChange = false
                    getNetworkAndChainId()

                    ethereum.on('chainChanged', handleNewChain)
                    ethereum.on('networkChanged', handleNewNetwork)
                    ethereum.on('accountsChanged', handleNewAccounts)

                    try {
                        const newAccounts = await ethereum.request({
                            method: 'eth_accounts',
                        })
                        handleNewAccounts(newAccounts)
                    } catch (err) {
                        console.error('Error on init when getting accounts')
                    }
                }
            }

            window.addEventListener('DOMContentLoaded', initialize)


            let accounts = [];



            async function getAccount() {
                accounts = await ethereum.request({
                    method: 'eth_requestAccounts'
                });
            }
        </script>
    </body>

    </html>

    <?php

    global $table_prefix, $wpdb;



    $table_name = $table_prefix . 'cryptochain';

    if (isset($_POST['waladd'])) {
        $wl = sanitize_text_field($_POST['waladd']);
    }

    if (isset($_POST['check'])) {
        $chk = sanitize_text_field($_POST['check']);
    } else {
        $chk = "off";
    }

    if (isset($_POST['mode'])) {
        $mod = sanitize_text_field($_POST['mode']);
    } else {
        $mod = "off";
    }

    if (isset($_POST['network'])) {
        $ntwk = sanitize_text_field($_POST['network']);
    }

    if (isset($_POST['chainId'])) {
        $chid = sanitize_text_field($_POST['chainId']);
    }

    if (isset($_POST['upid'])) {
        $uid = sanitize_text_field($_POST['upid']);
    }


    $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");


    if (isset($_POST['ins']) && $num_rows <= 0) {



        $wpdb->insert(
            $table_name,
            array(
                'wallet_address' => $wl,
                'display_status' => $chk,
                'mode' => $mod,
                'chain_id' => $chid,
                'network'  => $ntwk

            )
        );

        //echo "<script>alert('Address Inserted') </script>";


        echo "<meta http-equiv='refresh' content='0'>";
    }

    if (isset($_POST['ins']) && $num_rows >= 1) {

        $wpdb->update(
            $table_name,
            array(
                'wallet_address' => $wl,
                'display_status' => $chk,
                'mode' => $mod
            ),
            array(
                'id' => $uid
            )
        );
        echo "<meta http-equiv='refresh' content='0'>";
    }
?>

<?php
}