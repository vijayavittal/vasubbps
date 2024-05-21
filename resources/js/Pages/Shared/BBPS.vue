<template>
    <div class="h-3">
        <h1 class="text-primary self-center font-body font-bold text-xl text-center">{{type}}</h1>
        <div v-if="!loaded" class="flex flex-col justify-start h-full">
            <img src="https://c.tenor.com/5o2p0tH5LFQAAAAi/hug.gif" class="w-16 self-center align-middle">
        </div>
        <div v-if="loaded" class="flex flex-row justify-start h-full">
           
            <div class="w-1/2" v-if="billFetched && type !=='Mobile (Prepaid)' && type !== 'DTH' && type !== 'Life Insurance'">
                <p class="text-primary  font-medium  font-body text-md py-2">Select the Biller: </p>
                <select v-on:change="billerChanged" :class="selectBiller === '' ? 'border-primary border':'border-green-500 border-2' " class="w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2"  v-model="selectBiller">
                    <option value="">Biller List</option>
                    <option  v-for="(bank,i) in list" :value="i">{{bank.name}}</option>
                </select>
                <p-input v-on:input="inputChanged" v-for="(param,i)  in paramList" :key="param.param_name" :type="param.param_type" :name="param.param_desc" :min="param.min_length" :max="param.max_length" :index="i" />
                
                <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Customer Id"/>
                <button v-on:click="submitClicked" class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">{{submitMessage}}</button>
            </div>
            <!-- DTH-->
            <div class="w-1/2" v-if="type == 'DTH' && billFetched">
                <p class="text-primary  font-medium  font-body text-md py-2">Select the Biller: </p>
                <select v-on:change="billerChanged" :class="selectBiller === '' ? 'border-primary border':'border-green-500 border-2' " class="w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2"  v-model="selectBiller">
                    <option value="">Biller List</option>
                    <option  v-for="(bank,i) in list" :value="i">{{bank.name}}</option>
                </select>
                <div v-if="selectBiller == '0'"> <!--Tata play-->
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Subscriber Number"/>
                    <p-input v-model="mobile" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Mobile Number"/>
                    <p-input v-model="cash" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="cash"/>
                 </div>

                 <div v-if="selectBiller == '1'">
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Subscriber Number"/>
                    <p-input v-model="cash" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="cash"/>
                </div>
               
                <button v-on:click="DirectPay" class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">{{submitMessage}}</button>
                </div>
                <!--Life Insurance-->
                  <!--DTH-->
                 <div class="w-1/2" v-if="type == 'Life Insurance' && billFetched">
                    <p class="text-primary  font-medium  font-body text-md py-2">Select the Biller: </p>
                <select v-on:change="billerChanged" :class="selectBiller === '' ? 'border-primary border':'border-green-500 border-2' " class="w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2"  v-model="selectBiller">
                    <option value="">Biller List</option>
                    <option  v-for="(bank,i) in list" :value="i">{{bank.name}}</option>
                </select>
                <div v-if="selectBiller == '0' || selectBiller == '1'"> 
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Policy Number"/>
                    
                 </div>
                 <div v-else> 
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="date" min="1" name="Date Of Birth (DDMMYYYY)"/>
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Policy Number"/>
                    
                 </div>
                 <button v-on:click="submitClicked" class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">{{submitMessage}}</button>
                </div> 
              <!--Life Insurance-->
                  <!--Prepaid-->
              <div class="w-1/2" v-if="type == 'Mobile (Prepaid)' && billFetched">
                <p class="text-primary  font-medium  font-body text-md py-2">Select the Biller: </p>
                <select v-on:change="billerChanged" :class="selectBiller === '' ? 'border-primary border':'border-green-500 border-2' " class="w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2"  v-model="selectBiller">
                    <option value="">Biller List</option>
                    <option  v-for="(bank,i) in list" :value="i">{{bank.name}}</option>
                </select>
                <div v-if="selectBiller == '0'"> <!--Tata play-->
                    <p-input v-on:input="inputChanged" v-for="(param,i)  in paramList" :key="param.param_name" :type="param.param_type" :name="param.param_desc" :min="param.min_length" :max="param.max_length" :index="i" />
                
                <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Mobile Number"/>
                  
                    <select name="mobile" v-model="mobile" @change="fetchData" class="border-primary border w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2">
                        <option value="">State List</option>
<option value="Andhra Pradesh">Andhra Pradesh</option>
<option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
<option value="Arunachal Pradesh">Arunachal Pradesh</option>
<option value="Assam">Assam</option>
<option value="Bihar">Bihar</option>
<option value="Chandigarh">Chandigarh</option>
<option value="Chhattisgarh">Chhattisgarh</option>
<option value="Dadar and Nagar Haveli">Dadar and Nagar Haveli</option>
<option value="Daman and Diu">Daman and Diu</option>
<option value="Delhi">Delhi</option>
<option value="Lakshadweep">Lakshadweep</option>
<option value="Puducherry">Puducherry</option>
<option value="Goa">Goa</option>
<option value="Gujarat">Gujarat</option>
<option value="Haryana">Haryana</option>
<option value="Himachal Pradesh">Himachal Pradesh</option>
<option value="Jammu and Kashmir">Jammu and Kashmir</option>
<option value="Jharkhand">Jharkhand</option>
<option value="Karnataka">Karnataka</option>
<option value="Kerala">Kerala</option>
<option value="Madhya Pradesh">Madhya Pradesh</option>
<option value="Maharashtra">Maharashtra</option>
<option value="Manipur">Manipur</option>
<option value="Meghalaya">Meghalaya</option>
<option value="Mizoram">Mizoram</option>
<option value="Nagaland">Nagaland</option>
<option value="Odisha">Odisha</option>
<option value="Punjab">Punjab</option>
<option value="Rajasthan">Rajasthan</option>
<option value="Sikkim">Sikkim</option>
<option value="Tamil Nadu">Tamil Nadu</option>
<option value="Telangana">Telangana</option>
<option value="Tripura">Tripura</option>
<option value="Uttar Pradesh">Uttar Pradesh</option>
<option value="Uttarakhand">Uttarakhand</option>
<option value="Mumbai">Mumbai</option>
</select>
<div v-if="isLoading">Loading...</div>
    <div v-else>

        <select v-model="selectedPlanId" class="border-primary border w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2">
      <option value="">Select Plan</option>
      <option disabled><b>International Circle</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'INT_ROAMING'">{{ circle.planDescription }}</option>
      <option disabled><b>UNLIMITED</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'UNLIMITED'">{{ circle.planDescription }}</option>
      <option disabled><b>FULL TALK TIME</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'FULL TALK TIME'">{{ circle.planDescription }}</option>
      <option disabled><b>FRC</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'FRC'">{{ circle.planDescription }}</option>
      <option disabled><b>Recharge</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'RECHARGE'">{{ circle.planDescription }}</option>
      <option disabled><b>Topup</b></option>
      <option v-for="circle in circles" :value="circle.Id" :key="circle.id" v-if="circle.categoryType == 'TOPUP'">{{ circle.planDescription }}</option>
    </select>
    </div>
    <input type="text" v-model="cash" readonly class="border-primary border w-full appearance-none text-primary my-2 bg-secondary  rounded rounded-md py-2 px-2">

                 </div>

                 <div v-if="selectBiller == '1'">
                    <p-input v-model="amount" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="Subscriber Number"/>
                    <p-input v-model="cash" v-if="selectBiller !== '' && !fetch" type="text" min="1" name="cash"/>
                </div>
               
                <button v-on:click="payPrepaid" class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">{{submitMessage}}</button>
                </div>
                <!--prepaid -->
            <div class="w-1/2">
                <div class="flex flex-col overflow-y-scroll" v-if="success">
    <img class="w-32 self-center mb-2" src="../../../images/success_transaction.png">
    <p class="font-body text-primary font-medium self-center my-2">Transaction Successful</p>
    <p class="font-body text-primary font-medium self-center my-2">Payment Status:<b>{{paymentstatus}}</b></p>
    <p class="font-body text-primary font-medium self-center my-2">Transaction Id :<b>{{transid}}</b></p>
</div>

                <div v-if="submitEnabled" class="flex flex-col justify-center my-auto">
                    <table class="table-fixed">
 
 <tbody>
   <tr>
     <td class="border px-4 py-2">Biller Name</td>
     <td class="border px-4 py-2">{{name}}</td>
    
   </tr>
   <tr class="bg-gray-100">
     <td class="border px-4 py-2">Due Date</td>
     <td class="border px-4 py-2">{{duedate}}</td>
     
   </tr>
   <tr>
     <td class="border px-4 py-2">Amount</td>
     <td class="border px-4 py-2">Rs {{cash}}</td>
    
   </tr>
   <tr>
     <td class="border px-4 py-2">Pay</td>
     <td class="border px-4 py-2"> <button v-on:click="Paybill" class="w-full rounded rounded-md text-white py-2 my-2 bg-red-800">{{submitpay}}</button></td>
    
   </tr>
 </tbody>
</table>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import axios from "axios";
import PInput from "./P-Input";

export default {
    name: "BBPS",
    components: {PInput},
    props:['type','data'],
    data:function (){
      return{
          list:[],
          billPlans:[],
          loaded:false,
          paramList:[],
          selectBiller:'',
          success:false,
          params:[],
          amount:0,
          amt:0,
          mobile:'',
          categoryType:'',
          selectedCircle: '',
          circles: [],
          selectedPlanId: '',
          isLoading: false,
          dueDate:null,
          submitMessage:'Submit',
          submitpay:'Pay',
          fetch:null,
          submitEnabled:false,
          paramData:[],
          paramJson:'',
          billFetched:true,
          
          fetchIpayID:null,
          transactionId:null,
          location: {
              lat: null,
              long: null,
              detected:false
          },
          passportSuccess:'',
          cash:'',
          name:'',
          duedate:'',
          refid:null,
          transid:null,
          paymentstatus:null
         
       
      }
    },
    watch:{
        selectedPlanId(newVal) {
      const selectedCircle = this.circles.find(circle => circle.Id === newVal);
      if (selectedCircle) {
        this.cash = selectedCircle.amountInRupees;
      } else {
        this.cash = '';
      }
    },
        fetch(){
            if(this.fetch){
                this.submitMessage = "Fetch Bill"
            }else{
                this.submitMessage = "Submit"
            }
        },
        paramList(){

        },
        paramData(){

        }
    },
    mounted() {
        axios.post('./billers',{
            name:this.type
        }).then(response=>{

            this.list = response.data.data
            this.loaded = true;
        });
        this.checkGeoService();
    },
    methods:{
        fetchData() {
      

      this.isLoading = true;
      axios.post('./fetch-data',{
            mobile:this.mobile
        }).then(response => {
            this.circles = response.data.planDetails;
          this.isLoading = false;
        })
        .catch(error => {
          console.error('Error fetching data:', error);
          this.isLoading = false;
        });
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
        billerChanged:function (){
            this.paramData = [];
            this.paramList = [];
            this.amount = 0;
            this.fetch = false;
            if(this.type === 'Mobile (Prepaid)' && this.selectBiller !== '' ){

            }
            this.fetch = this.list[this.selectBiller].bill_fetch === "Y";
            this.paramJson = JSON.parse(this.list[this.selectBiller].params);

            if( Array.isArray(this.paramJson.item)){
                this.paramList = this.paramJson.item;
            }else{
                this.paramList.push(this.paramJson.item)
            }
            for(let i=0;i<this.paramList.length;i++){
                this.paramData.push({value:'',validated:false});
            }

        },
        submitClicked:function (){     
            axios.post('./fetch-bill',{
                sp_key:this.list[this.selectBiller].sp_key,               
                params:this.paramData,                    
                location:this.location,                   
                amount:this.amount  
                }).then(response=>{
                    if(response.data.status === "success"){
                        this.cash =  response.data.data.response.billerResponse.amount;
                        
                        this.duedate =   response.data.data.response.billerResponse.dueDate;
                        this.name=  response.data.data.response.billerResponse.customerName;
                        this.refid =  response.data.data.response.refId;
                        this.submitpay = "Pay "+this.cash;
                        // this.PassportSuccess = true;
                        this.submitEnabled = true;
                        this.billFetched = false;

                    }else{
                        alert(response.data.message);
                        //  this.submitEnabled = false;
                        //  this.billFetched = true;
                     }
                });        
                                                 
         },
         Paybill:function() {
    axios.post('./pay-bill', {
        sp_key: this.list[this.selectBiller].sp_key,
        params: this.paramData,
        refId: this.refid,
        cash: this.cash,
        amount: this.amount,
        location: this.location,
        mobile: this.mobile
    }).then(response => {
        if (response.data.status === "success") {
            this.success = true;
            this.submitEnabled = false;
            this.paymentstatus = response.data.data.clientResponse.response.responseReason;
            this.transid = response.data.data.clientResponse.response.txnReferenceId;
        } else {
            alert(response.data.message);// Alert response reason on failure
            this.success = false;
        }
    });
},

         DirectPay:function(){
            axios.post('./pay-Bill-Double',{
                sp_key:this.list[this.selectBiller].sp_key,               
                params:this.paramData,                    
                refId:this.refid,              
                cash:this.cash,
                  amount:this.amount ,
                  location:this.location,
                  
                  mobile:this.mobile        
                }).then(response=>{
                    if(response.data.status === "success"){
                        this.success = true;
                        this.billFetched = false;
                        this.paymentstatus = response.data.data.clientResponse.response.responseReason;
                        this.transid = response.data.data.clientResponse.response.txnReferenceId;

                      }else{
                         alert(response.data.message)
                         this.submitEnabled = false;
                     }
                });     
         },
         payPrepaid:function(){
            axios.post('./pay-Bill-Prepaid',{
                sp_key:this.list[this.selectBiller].sp_key,               
                params:this.paramData,                    
                      
                cash:this.cash,
                  amount:this.amount ,
                  location:this.location,
                  selectedPlanId:this.selectedPlanId,
                  
                  mobile:this.mobile        
                }).then(response=>{
                    if(response.data.status === "success"){
                        this.success = true;
                        this.billFetched = false;
                        this.paymentstatus = response.data.data.clientResponse.response.responseReason;
                        this.transid = response.data.data.clientResponse.response.txnReferenceId;

                      }else{
                         alert(response.data.message)
                         this.success = false;
                     }
                });     
         },
         
        inputChanged:function(value,validate,i){
            this.billFetched = false;
            this.submitMessage = this.fetch ? "Fetch Bill":"submit";
            this.submitEnabled = true;
            this.paramData[i].value = value;
            this.paramData[i].validated =validate
        }
        
    }
}
</script>

<style scoped>

</style>
