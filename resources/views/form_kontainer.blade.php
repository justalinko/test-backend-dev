<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <title>Logic Test Kontainer</title>
</head>
<body>
    <div class="container">
        <div class="flex flex-col min-h-screen min-w-screen justify-center items-center">
          
            <div class="bg-white w-10/12 p-5 rounded-sm shadow-lg">
                <header class="mb-5">
                    <h1 class="text-xl font-weight-bold">Logic Test</h1>              
                </header>
                
             
                    <div class="mb-3">
                        <label class="block mb-3">Nomer Kontainer</label>
<textarea name="nomer" style="height:300px" id="nomer" class="border-solid border-2 border-gray-200 rounded-sm p-2 w-full" >
3137
1367
2333
2001                        
</textarea>
                    </div>
                    <div class="mb-3">
                        <button id="btn" onclick="getApi()" type="button" class="bg-gray-600 text-white items-center flex flex-col w-full justify-center rounded-sm p-3 font-bold disabled">
                            Get Result
                        </button>
    
    <button  onclick="document.getElementById('nomer').innerHTML = getRandomNumber();" type="button" class="bg-black mt-3 text-white items-center flex flex-col w-full justify-center rounded-sm p-3 font-bold disabled">
                            Generate new number
                        </button>
                    </div>
               <div id="result"></div>
            </div>
        </div>
    </div>
<script type="text/javascript">
function getApi() {
    var nomer = document.getElementById('nomer').value;
    if(nomer =='' || nomer == null)
    {
        alert(' Nomer kosong , mohon isi terlebih dahulu');
    }else{
    var nomers=nomer.replace('\r','').split('\n');
    nomers.forEach(function(v,k){
   

    fetch('/api/parse-number-kontainer/'+v)
    .then(resp => resp.json())
    .then(data=>{
        console.log(data);
        var result = '<div class="mt-2 bg-green-300 text-green-700 rounded p-3">NUMBER : '+data.number+' | PRIMA : '+data.isPrima+' | RESULT : '+data.result+'</div>'
        document.getElementById('result').insertAdjacentHTML('beforeend',result);
    })

  

    })
    }
}
function getRandomNumber()
{
 
    var data = ""
    for (let index = 0; index <= 10; index++) {
        var rand1 = Math.floor(Math.random() * 100);
    var rand2 = Math.floor(Math.random() * 999999);
    var rand3 = Math.floor(Math.random() * 999999999999999);
    var cc = rand1 + rand2 + rand3;
        data+=cc.toString().substr(0,7);
        data+="\n"
        
    }
    return data;
}

window.onload = function(){

    document.getElementById('nomer').innerHTML = getRandomNumber();
}
</script>
</body>
</html>