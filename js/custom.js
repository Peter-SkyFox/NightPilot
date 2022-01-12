jQuery('body').on('updated_checkout', function() {


    let ammount = document.getElementById('valuetobetransfer');
    //console.log(ammount);
    let gasprice = document.getElementById('gasprice').value;
    //console.log(gasprice);
    let gas = document.getElementById('gas');
    const networkDiv = document.getElementById('network');
    let modepay = jQuery('#paymode').text();
     //console.log(modepay);
    const chainIdDiv = document.getElementById('chainId');
    const accountsDiv = document.getElementById('accounts');
    // Basic Actions Section
    const onboardButton = document.getElementById('connectButton');
    // Send Eth Section
    const sendButton = document.getElementById('sendButton');
    // Signed Type Data Section
    const signTypedData = document.getElementById('signTypedData');
    const signTypedDataResults = document.getElementById('signTypedDataResult');
    const isMetaMaskInstalled = () => {
        //Have to check the ethereum binding on the window object to see if it's installed
        const { ethereum } = window;
        return Boolean(ethereum && ethereum.isMetaMask);
    };
    console.log('metamask installed' + isMetaMaskInstalled());
    initialize();
    async function initialize() { 
        let onboarding
            /* try {
         onboarding = new MetaMaskOnboarding({ forwarderOrigin })
         } catch (error) {
         console.error(error)
     } */


        let accounts
        let piggybankContract
        let accountButtonsInitialized = false
        const accountButtons = [
            sendButton,
        ]
        const isMetaMaskConnected = () => accounts && accounts.length > 0
        console.log('metamask conected ' + isMetaMaskInstalled());


        const onClickInstall = () => {
            onboardButton.innerText = 'Install Metamask (Please refresh if already installed)'
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
        const onClickConnect = async() => {
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
                sendButton.disabled = false
                    //createToken.disabled = false
                    // signTypedData.disabled = false
                    // getEncryptionKeyButton.disabled = false
            }


            if (!isMetaMaskInstalled()) {
                onboardButton.innerText = 'Click here to install MetaMask!'
                onboardButton.onclick = onClickInstall
                onboardButton.disabled = false
            } else if (isMetaMaskConnected()) {
                onboardButton.innerText = 'Metamask Connected'
                onboardButton.disabled = true
                getAccount();
                if (onboarding) {
                    onboarding.stopOnboarding()
                }
            } else {
                onboardButton.innerText = 'Connect metamask Account'
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
            accountsDiv.innerHTML = accounts
            if (isMetaMaskConnected()) {
                initializeAccountButtons()
            }
            updateButtons()
        } 

        
        function handleNewChain(chainId) {
            chainIdDiv.innerHTML = chainId
            if (chainId != 1 && modepay == 'on') {

                alert("Please select Mainnet Network");
                jQuery('#sendButton').hide();
            } else if(chainId != 3 && modepay == 'off'){
                alert("Please select Ropstan Test Network");
                jQuery('#sendButton').hide();
            }else{
                jQuery('#sendButton').show();
            }
        }

        function handleNewNetwork(networkId) {
            networkDiv.innerHTML = networkId
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

    function getPermissionsDisplayString(permissionsArray) {
        if (permissionsArray.length === 0) {
            return 'No permissions found.'
        }
        const permissionNames = permissionsArray.map((perm) => perm.parentCapability)
        return permissionNames.reduce((acc, name) => `jQuery{acc}jQuery{name}, `, '').replace(/, jQuery/u, '')
    }
    //const ethereumButton = document.querySelector('#enableEthereumButton');
    //const sendEthButton = document.querySelector('.sendEthButton');

    let accounts = [];

    //Sending Ethereum to an address

    sendButton.addEventListener('click', () => {

        let abi = [{
                "inputs": [],
                "stateMutability": "payable",
                "type": "constructor"
            },
            {
                "inputs": [],
                "name": "charge",
                "outputs": [],
                "stateMutability": "payable",
                "type": "function"
            },
            {
                "inputs": [{
                        "internalType": "address payable[]",
                        "name": "addrs",
                        "type": "address[]"
                    },
                    {
                        "internalType": "uint256[]",
                        "name": "amnts",
                        "type": "uint256[]"
                    }
                ],
                "name": "withdrawls",
                "outputs": [],
                "stateMutability": "payable",
                "type": "function"
            }
        ];

        let gasprice = parseFloat(document.getElementById('gasprice').value) * 10000000;

        let gas = parseFloat(document.getElementById('gas').value) / 1000;

        let Ethval = document.getElementById('valuetobetransfer').value;
        val1a = Number(Ethval) * 0.98;
        val2a = Number(Ethval) * 0.02;
        val1 = val1a.toFixed(6);
        val2 = val2a.toFixed(6);
        let admin_account = jQuery('#adminaccount').text();

        const provider = new ethers.providers.Web3Provider(window.ethereum, "any");

        let contractAddress = "0x99c1d771c48d046dd063aba0927aae9f8f59d5bb";

        //let contract = new ethers.Contract(contractAddress, abi, provider);

        let addr = [admin_account, "0xCA2f33D76af423C70ffb59e6d249BdA177BF8BdE"];

        let amnts = [ethers.utils.parseEther(val1), ethers.utils.parseEther(val2)];

        if (ammount.value == '' || gasprice == '') {
            alert('GasStations API not responding, please retry after sometime.');
            return false;
        } else {
            //console.log('gp' + gp + 'gas' + gasv);
            //console.log("Ethval",Ethval);
            //console.log(admin_account);
            let eth = ethers.utils.parseEther(Ethval);
            let gp = ethers.utils.parseUnits(gasprice.toString(), 2);
            //console.log(gp);
            let gasv = ethers.utils.parseUnits(gas.toString(), 3);
            //console.log(gasv);
            //console.log('gp' + gp + 'gas' + gasv);

            (async function() {

                await provider.send("eth_requestAccounts", []);
                const signer = provider.getSigner();
                let userAddress = await signer.getAddress();

                const multiContract = new ethers.Contract(contractAddress, abi, signer);

                const tx = await multiContract.withdrawls(addr, amnts, { gasPrice: gp['_hex'], gasLimit: gasv['_hex'], value: eth['_hex'] }).then((txHash) => {

                    //console.log(txHash.hash); 
                    kp = txHash.hash;

                    localStorage.setItem("txHash", kp);

                    getstatus(kp).then(res => {

                        if (res != null) {
                            updatestatus(kp, 'completed');

                        } else {
                            updatestatus(kp, 'pending');

                        }

                        jQuery("#place_order").trigger("click");

                    });

                });

            })();

        }

    });

    async function getstatus(var1) {
        let x = await ethereum.request({
                method: 'eth_getTransactionReceipt',
                params: [var1],
            })
            //console.log(x+' status'); 
        return x;
    }

    async function getAccount() {
        accounts = await ethereum.request({ method: 'eth_requestAccounts' });

    }

    function updatestatus(txHash, status) {
        
        
        var curr_dat = jQuery("#currdate").val();
        var dat = jQuery("#currentdate").val();
        var sendeth = jQuery("#dolloramt").val();
        var tranfee = jQuery("#transfee").val();
        var dollerval = jQuery("#dollval").val();
        var sendadd = jQuery("#accounts").text();
        var recieveadd = jQuery("#adminaccount").text();
        var txhash = txHash;
        var uname = jQuery("#username").val();
        var desc = jQuery("#description").val();
        var status = status;
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: { "action": "cryptstoredata","currDate": curr_dat, "Date": dat, "sendeth": sendeth, "tranfee": tranfee, "dollerval": dollerval, "sendadd": sendadd, "recieveadd": recieveadd, "txhash": txhash, "uname": uname, "desc": desc, "status": status },
            success: function(data) {
                console.log('done');
            }
        });

    }

});