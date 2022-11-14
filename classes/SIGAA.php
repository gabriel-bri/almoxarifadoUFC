<?php  
   
   class SIGAA {
      private $curl;
      private $dados;
      public function chamarAPI() {
         $url = "https://sigaapi.gabrielfvale.vercel.app/sigaa";

         $this->curl = curl_init($url);
         curl_setopt($this->curl, CURLOPT_URL, $url);
         curl_setopt($this->curl, CURLOPT_POST, true);
         curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

         $headers = array(
            "Content-Type: application/json",
         );
         curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
      }

      public function passarDados($user, $senha) {
         $data = '{"login": "' . $user . '", "senha": "' . $senha . '"}';
         curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

         //for debug only!
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
      }

      public function pegarDados() {
         $resp = curl_exec($this->curl);
         curl_close($this->curl);
         $this->dados = json_decode($resp);
         return $this->dados;
      }
   }
?>