<template>
<div class="flex flex-col justify-center">
    <h1 class="text-center text-primary font-body font-medium text-xl">AEPS</h1>
    <div v-if="!loaded" class="flex flex-col justify-start h-full">
        <img src="https://c.tenor.com/5o2p0tH5LFQAAAAi/hug.gif" class="w-16 self-center align-middle">
    </div>
    <div v-if="loaded" class="flex flex-row justify-between">
        <div class="w-1/2 flex flex-col justify-center">
            <p class="text-primary  font-medium  font-body text-md py-2">Select the Bank : </p>
            <select :class="bankIIN === '' ? 'border-primary border':'border-green-500 border-2' " class="appearance-none text-primary my-2 bg-secondary focus:outline-none  rounded rounded-md py-2 px-2"  v-model="bankIIN">
                <option value="">Bank List</option>
                <option  v-for="bank in banks" v-if="bank.aeps_enabled === '1'"  :value="bank.bank_iin">{{bank.bank_name}}</option>
            </select>
            <p class="text-primary  font-medium font-body text-md">Aadhar number : </p>
            <input placeholder="Aadhar Number" :class="(aadharNumber === null || aadharNumber.length !== 12) ? 'border-primary border':'border-green-500 border-2' " v-model="aadharNumber" type="number" class="focus:outline-none text-primary my-2 bg-secondary py-2 px-2 rounded rounded-md">
            <p class="text-primary  font-medium font-body text-md">Phone Number : </p>
            <input placeholder="Phone Number" :class="(mobile === null || mobile.length !== 10) ? 'border-primary border':'border-green-500 border-2' " v-model="mobile" type="number" class="focus:outline-none text-primary my-2 bg-secondary py-2 px-2 rounded rounded-md">
            <p class="text-primary  font-medium font-body text-md">Transaction Type : </p>
            <select :class="transactionType === '' ? 'border-primary border':'border-green-500 border-2' " class="focus:outline-none appearance-none text-primary my-2 bg-secondary rounded rounded-md  py-2 px-2"  v-model="transactionType">
                <option value="">Transaction List</option>
                <option  v-for="type in transactionsMenu"  :value="type.value">{{type.name}}</option>
            </select>
            <p  v-if="transactionType === 'WAP'" class="text-primary font-medium  font-body text-md">Amount</p>
            <input v-if="transactionType === 'WAP'" placeholder="Amount" :class=" amount === null ? 'border-primary border':'border-green-500 border-2' " v-model="amount" type="number" class="focus:outline-none w-full text-primary my-2 bg-secondary py-2 px-2 rounded rounded-md">
            <button v-on:click="submitAeps" :class="submitEnabled ? 'bg-primary' : 'bg-red-200'" :disabled="!submitEnabled" class="px-2 py-2 rounded-md rounded mt-3  text-secondary">Submit</button>
        </div>
        <div class="w-1/2 flex flex-col justify-center">
            <div class="flex flex-col justify-center" v-if="rdLoaded">
                <img class="w-32 self-center mb-4" :src=imageAddress>
                <p class="self-center text-center">{{rdMessage}}</p>
            </div>
            <div class="flex flex-col overflow-y-scroll" v-if="statementLoaded">
                <p class="font-body text-primary font-medium self-center my-2">Please find the Mini-Statement below</p>
                <div v-for="statement in statementTransaction" class="flex flex-row justify-between bg-white mx-2 my-1 rounded-md">
                    <div class="flex flex-col justify-start mx-1 my-1">
                        <p class="font-body text-primary font-medium py-1 px-2">{{statement.narration}}</p>
                        <p class="font-body text-primary text-sm py-1 px-2 font-normal">{{statement.date}}</p>
                    </div>
                    <p class="font-body text-primary self-center text-left font-medium py-1 px-2">Rs {{statement.amount}} {{statement.txnType}}</p>
                </div>
            </div>
            <div class="flex flex-col overflow-y-scroll" v-if="balanceLoaded">
                <p class="font-body text-primary font-medium self-center my-2">Requested Balance</p>
                <p class="font-body text-primary text-xl font-medium self-center my-2">Rs {{balance}}</p>
            </div>
            <div  class="flex flex-col overflow-y-scroll" v-if="withdrawalLoaded">
                <img class="w-32 self-center mb-2" src="../../../images/success_transaction.png">
                <p class="font-body text-primary font-medium self-center my-2">Transaction Successful</p>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from "axios";

export default {
    name: "ATM",
    data:function (){
        return{
            location:{
                detected:false,
                lat:null,
                long:null,
            },
            rdService:{
                detected:false,
                info:null
            },
            loaded:false,
            withdrawalLoaded:false,
            rdLoaded:false,
            rdMessage:"Checking for Geo Location",
            banks:null,
            bankIIN:'',
            aadharNumber:null,
            mobile:null,
            amount:null,
            balance:0,
            transactionType:'',
            submitEnabled:true,
            userAgent:navigator.userAgent,
            balanceLoaded:false,
            statementTransaction:[],
            transactionsMenu: [
                    {name:'Withdrawal',value:'WAP'},
                    {name:'Mini Statment',value:'SAP'},
                    {name:'Balance Check',value:'BAP'}
            ],
            statementLoaded:false,
            imageAddress:"https://c.tenor.com/5o2p0tH5LFQAAAAi/hug.gif"

        }
    },
    watch:{
        banks : function (){
        }
    },
    mounted() {
        axios.get('./get-banks').then((response)=>{
            if(response.status === 200 || response.data.status === "success"){
                this.banks = response.data.data.item
                this.loaded = true
            }else{
                alert("Please try after sometime")
            }
        })
        this.checkGeoService();
        this.checkRDService();
    },
    methods:{
        clearAepsForm:function (){
          this.mobile = null;
          this.rdLoaded = false;
          this.bankIIN='';
          this.aadharNumber=null;
          this.transactionType='';
          this.submitEnabled=true;
          this.imageAddress="https://c.tenor.com/5o2p0tH5LFQAAAAi/hug.gif"
          this.statementLoaded = false;
          this.balanceLoaded = false;
        },
        submitAeps : function (){
            this.submitEnabled = false;
            this.statementLoaded=false;
            this.balanceLoaded=false;

            if(this.bankIIN === ''){
                alert("Please select appropriate bank option");
                this.submitEnabled = true;
                return
            }
            if (this.aadharNumber === null || this.aadharNumber.length !== 12 ){
                alert("Please enter correct aadhar details");
                this.submitEnabled = true;
                return;
            }
            if (this.mobile === null || this.mobile.length !== 10 ){
                alert("Please enter correct contact details");
                this.submitEnabled = true;
                return;
            }
            if (this.transactionType === ''){
                alert("Please select transaction type");
                this.submitEnabled = true;
                return;
            }
            if (this.transactionType === 'WAP' && this.amount == null){
                alert("Please enter amount detials");
                this.submitEnabled = true;
                return;
            }
            this.rdLoaded = true;

            if (!this.location.detected){
                alert("AEPS will not work without Location Services. Please enable them");
                this.checkGeoService();
                this.submitEnabled = true;
                this.rdLoaded = false;
                return;
            }
            this.rdMessage = "Checking RD Service"

            if (!this.rdService.detected){
                alert("AEPS will not work without RD Services. Please connect fingerprint Device,start RD Service and refresh the page");
                this.checkRDService();
                this.submitEnabled = true;
                this.rdLoaded = false;
                return;
            }


            this.rdMessage = "Please put fingerprint"

            this.captureFingerprint();


        },
        checkGeoService(){
             navigator.geolocation.getCurrentPosition((position)=>{
                this.location.lat = position.coords.latitude;
                this.location.long = position.coords.longitude;
                this.location.detected = true;
                 this.$emit('geoService',this.location.detected)
            }, ()=>{
                this.location.detected = false;
                 this.$emit('geoService',this.location.detected)
                alert("Location permission is required to proceed. Please refresh to give permission");
            });



        },
        checkRDService() {
            this.selectedComponent = 'atm';
            var xhr = new XMLHttpRequest();
            xhr.open('RDSERVICE', "http://127.0.0.1:11100", true);
            xhr.responseType = 'text';
            xhr.context = this;
            xhr.onload = function () {
                var status = xhr.status;
                if (status === 200) {
                    let parser = new DOMParser();
                    let xmlDoc = parser.parseFromString(xhr.response, "text/xml");
                    if (xmlDoc.getElementsByTagName("RDService")[0].attributes[0].nodeValue === "READY") {
                        this.context.rdService.detected = true;
                        this.context.rdService.info = xmlDoc.getElementsByTagName("RDService")[0].attributes[1].nodeValue
                        this.context.$emit('rdService', this.context.rdService.detected)
                    } else {
                        this.context.rdService.detected = false;

                        this.context.$emit('rdService', this.context.rdService.detected)
                    }
                }
            }
            xhr.send();
        },
        captureFingerprint(){
            var xhr = new XMLHttpRequest();
            xhr.open('CAPTURE', "http://127.0.0.1:11100/rd/capture", true);
            let inputXML = "<PidOptions> <Opts fCount=\"1\" fType=\"0\" iCount=\"0\" pCount=\"0\" format=\"0\" pidVer=\"2.0\" timeout=\"20000\" otp=\"\" posh=\"UNKNOWN\" env=\"P\" wadh=\"\" /> <Demo></Demo> <CustOpts> <Param name=\"ValidationKey\" value=\"\" /> </CustOpts> </PidOptions>"
            xhr.responseType = 'text';
            xhr.context = this;
            xhr.onload = function () {
                var status = xhr.status;
                if (status === 200) {
                    let parser = new DOMParser();
                    let xmlDoc = parser.parseFromString(xhr.response, "text/xml");
                    if(xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Resp")[0].attributes[0].nodeValue === "0"){
                        this.context.imageAddress = "https://c.tenor.com/kGFp0e2m_RsAAAAd/success.gif"
                        this.context.rdMessage = "Fingerprint Successfully Captured"
                        let RespXML = xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Resp")[0];
                        let DeviceXML = xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("DeviceInfo")[0]
                        let SKeyXML = xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Skey")[0]
                        let DataXML = xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Data")[0]
                        let HmacXML = xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Hmac")[0]
                        this.context.imageAddress="https://c.tenor.com/5o2p0tH5LFQAAAAi/hug.gif";
                        this.context.rdMessage = "Submitting transaction. Please wait....."
                        axios.post('./aeps-transaction',{
                            amount:this.context.amount === null ? 0 :this.context.amount,
                            aadhar_uuid:this.context.aadharNumber,
                            bankiin:this.context.bankIIN,
                            latitude:this.context.location.lat,
                            longitude:this.context.location.long,
                            mobile:this.context.mobile,
                            sp_key:this.context.transactionType,
                            ci:SKeyXML.attributes[0].nodeValue,
                            dc:DeviceXML.attributes[5].nodeValue,
                            dpid:DeviceXML.attributes[0].nodeValue,
                            errCode:RespXML.attributes[0].nodeValue,
                            errInfo:RespXML.attributes[1].nodeValue,
                            fCount:1,
                            tType:null,
                            hmac:HmacXML.innerHTML,
                            iCount:0,
                            mc:DeviceXML.attributes[4].nodeValue,
                            mi:DeviceXML.attributes[3].nodeValue,
                            nmPoints:RespXML.attributes[4].nodeValue,
                            qScore:RespXML.attributes[5].nodeValue,
                            pCount:0,
                            pType:"",
                            rdsId:DeviceXML.attributes[1].nodeValue,
                            rdsVer:DeviceXML.attributes[2].nodeValue,
                            sessionKey:SKeyXML.innerHTML,
                            srno:DeviceXML.getElementsByTagName("additional_info")[0].getElementsByTagName("Param")[0].attributes[1].nodeValue,
                            pidData:DataXML.innerHTML,
                            pidDataType:DataXML.attributes[0].nodeValue,
                            userAgent:this.context.userAgent
                        }).then(response=>{
                            console.log(response.data);
                            if(response.data.status === "failure"){
                                alert("Transaction Failed due to "+response.data.message);
                                this.context.clearAepsForm();
                            }else{
                                if(this.context.transactionType === "SAP"){
                                    this.context.rdLoaded = false;
                                    this.context.statementTransaction = response.data.data.data.mini_statement.item;
                                    this.context.statementLoaded = true;
                                }else if(this.context.transactionType === "BAP"){
                                    this.context.rdLoaded = false;
                                    this.context.balance = response.data.data.data.balance;
                                    this.context.balanceLoaded = true;
                                }else if(this.context.transactionType === "WAP"){
                                    this.context.rdLoaded = false;
                                    this.context.withdrawalLoaded = true;
                                }
                                this.context.submitEnabled = true;
                            }
                        });
                    }else{
                        this.context.submitEnabled = true;
                        this.context.rdMessage = "Failure ! "+xmlDoc.getElementsByTagName("PidData")[0].getElementsByTagName("Resp")[0].attributes[1].nodeValue
                    }

                }
            }
            xhr.send(inputXML);
        }

    }
}
</script>

<style scoped>

</style>
