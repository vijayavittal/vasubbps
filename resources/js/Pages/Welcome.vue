<template>
    <div class="relative h-screen">
        <div v-if="Object.keys(errors).length !==0 " id="errorMessage" class="mt-5 z-50 absolute top-0 flex flex-col justify-center w-full">
            <div class="self-center w-1/2 bg-yellow-200 border border-red-400 text-red-700 px-4 py-3 rounded relative " role="alert">
                <p><strong class="font-bold">{{ errors[0] }}</strong></p>
            </div>
        </div>
        <div class="flex flex-row">
            <div class="relative w-3/5">
                <div class="absolute ml-8 flex-col flex z-30">
                    <div class=" mt-5 mb-4 flex flex-row content-center">
                        <img class="w-16" src="../../images/logo.png">
                        <div class="ml-3 w-32 flex flex-col justify-center">
                            <img class="w-32 self-center" src="../../images/KADAMBA.png">
                        </div>
                    </div>

                    <p class=" mt-3 font-body font-extrabold text-6xl text-red-600 leading-none">KADAMBA<br class=""/>Micro ATM<br/></p>
                    <p class=" mt-3 font-body font-extrabold text-xl text-red-600 leading-none">Utility services and more</p>
                    <div class=" mt-16 flex flex-col">
                        <h3 class="text-red-600 font-body">Register here </h3>
                        <div class="flex mt-2 flex-row">
                            <form @submit.prevent="clickRegister" id="phoneForm">
                                <input v-model="registerForm.phone" id="phone" name="phone" required class="px-3 border-b-2 border-0 bg-transparent border-red-600 font-body text-xl text-red-500 placeholder-red-600" type="number" placeholder="Phone Number" >
                                <button type="submit" id="registerButtonModal" class="ml-3 bg-red-600 px-4 py-2 font-body text-gray-100 rounded-md" >Register</button>
                            </form>
                        </div>
                    </div>
                    <p class=" mt-8 font-body font-medium text-xl text-red-600">Serve your customer with better amenities<br/>
                        with aadhar based
                        enabled payments at your<br/>
                        Door Step</p>
                    <div class="flex flex-row justify-center content-end">
                        <img class="w-48 mt-8" src="../../images/aeps_logo.png">
                        <a href="/terms" id="terms" class="text-red-600 font-bold mx-8 self-end cursor-pointer">Terms & Conditions</a>
                        <a href="/privacy" id="privacy" class="text-red-600 font-bold self-end cursor-pointer">Privacy Policy</a>
                    </div>

                </div>

            </div>
            <div class="relative flex-col flex w-3/5 h-screen">
                <img class="absolute left-0 top-0 h-screen w-full z-0" src="../../images/background.png">
                <img class="absolute right-0 mt-32 w-full z-10" src="../../images/aadhar_background.png">
                <button v-on:click="clickLogin" class="self-end mt-5 mr-6 font-body w-32 px-4 py-2 text-red-600 rounded-md text-lg bg-white z-10">LOGIN</button>
            </div>
        </div>
        <div v-if="loginModal" id="loginModal" class="absolute top-0 w-full h-screen z-30 opacity-50 bg-black ">
        </div>
        <div v-if="loginPage" id="loginPage" class=" absolute w-full top-0 z-50 flex flex-row h-screen justify-end">
            <img class="bg-fixed h-screen self-end w-4/12" src="../../images/login_background.png">
            <span v-on:click="closeLoginModal" id="closeLogin" class="absolute mr-6 text-white font-bold p-5 cursor-pointer">X</span>
            <div class="absolute mt-10 w-4/12 top-0 flex flex-col justify-center">
                <h1 class="self-center mt-24 text-white font-body font-bold text-3xl">LOGIN</h1>
                <p class="self-center mt-5 text-white font-body font-medium text-lg">Please enter the following details to login</p>
                <form class="flex-col flex justify-center" method="POST" action="./login" @submit.prevent="submit" id="loginForm">

                    <input v-model="loginForm.email" name="email" id="email" required class="self-center mt-12 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" placeholder="Username">
                    <input v-model="loginForm.password" name="password" id="password" class="self-center mt-6 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="password" placeholder="Password">
                    <button type="submit" class="mt-8 px-4 py-3 bg-red-800 text-white w-2/6 self-center">Login</button>
                    <button id="forgot" class=" mt-4 px-4 py-3 bg-red-800 text-white w-2/6 self-center">Forgot Password</button>
                </form>

            </div>

        </div>
        <div v-if="showRegisterForm" id="registerPage" class="absolute w-full top-0 z-40 flex flex-row h-screen justify-end">
            <img class="bg-fixed h-screen self-end w-4/12 " src="../../images/login_background.png">
            <span v-on:click="showRegisterForm = false" id="closeRegister" class="absolute mr-6 text-white font-bold p-5 cursor-pointer z-50">X</span>
            <div class="absolute mt-1 w-4/12 top-0 flex flex-col justify-center h-screen">
                <form class="flex flex-col justify-center" id="registerForm"  @submit.prevent="submitRegistration" >
                    <h1 class="self-center mt-2 text-white font-body font-bold text-3xl">REGISTER</h1>
                    <p class="self-center mt-2 text-white font-body font-medium text-lg">Please enter the following details to login</p>
                    <input v-model="registerForm.name" name="name" id="name" required class="self-center mt-2 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" placeholder="Name"/>
                    <input v-model="registerForm.company" name="company" id="company" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" placeholder="Company Name"/>
                    <input v-model="registerForm.address" name="address" id="address" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" placeholder="Address"/>
                    <input v-model="registerForm.email" name="email" id="email" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" placeholder="Email"/>
                    <input v-model="registerForm.pan" name="pan" id="pan" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100 uppercase" maxlength="10" type="text" placeholder="PAN"/>
                    <input v-model="registerForm.pincode" name="pincode" id="pincode" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="text" maxlength="6" placeholder="PINCODE"/>
                    <input v-model="registerForm.password" name="password" id="password" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="password" placeholder="Password"/>
                    <input v-model="registerForm.password_confirmation" name="password_confirmation" id="password_confirmation" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" type="password" placeholder="Confirm Password"/>
                    <input v-model="registerForm.otp" name="otp" id="otp" required class="self-center mt-4 w-4/6 px-3 border-b-2 border-0 bg-transparent border-gray-400 font-body text-xl text-gray-100" maxlength="6" type="password" placeholder="OTP"/>
                    <button id="registerFormButton" class="mt-8 px-4 py-3 bg-red-800 text-white w-2/6 self-center">Register</button>
                </form>
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: "Welcome",
    props: {
        errors: Object,
    },
    data:function (){
        return {
            loginModal:false,
            loginPage:false,
            loginForm:{
                email:null,
                password:null
            },
            registerForm:{
                phone:null,
                name:null,
                company:null,
                address:null,
                email:null,
                pan:null,
                pincode:null,
                password:null,
                password_confirmation:null,
                otp:null
            },
            showRegisterForm:false
        }
    },
    mounted() {

        // forgotButton.onclick = function (e){
        //     e.preventDefault();
        //     const form = document.getElementById("loginForm");
        //     if(form.reportValidity()){
        //         form.action = "/forgot";
        //         console.log(form);
        //         form.submit();
        //     }
        //
        // }
    },
    methods:{
        clickLogin : function (){
            this.loginModal = true;
            this.loginPage = true;
        },
        submit(){
            this.$inertia.post('./login',this.loginForm)
        },
        closeLoginModal(){
            this.loginModal = false;
            this.loginPage = false;
        },
        clickRegister(){
            axios.post("./phone",{_token: this.$page.props.csrf_token, phone: this.registerForm.phone},
            ).then((response)=>{
                if (response.data.status !== "success"){
                    alert(response.data.message);
                }else{
                    this.showRegisterForm = true;
                }
            });
        },
        submitRegistration(){
            this.$inertia.post('./register',this.registerForm)
        }

    }
}
</script>

<style scoped>

</style>
