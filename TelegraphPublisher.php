<?php
class TelegraphPublisher 
{
    private string $apiBaseUrl = 'https://api.telegra.ph/';
    private ?string $accessToken = null;

    protected function sendRequest(string $url, array $params)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Вимикаємо перевірку SSL для локального сервера (MAMP)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            $error = curl_error($ch);

            throw new Exception("cURL помилка: " . $error);
        }
        
        return json_decode($result, true) ?? [];
    }

    public function initAccount(string $shortName = 'AffnetixBot', string $authorName = 'Affnetix Developer')
    {
        $tokenFile = 'token.txt';

        if(file_exists($tokenFile)) {
            $this->accessToken = trim(file_get_contents($tokenFile));
            return;
        }
        $url = $this->apiBaseUrl . 'createAccount';
        $params = [
            'short_name' => $shortName,
            'author_name' => $authorName
            ];

        $response = $this->sendRequest($url, $params);

        if (!empty($response['result']['access_token'])) {
            $this->accessToken = $response['result']['access_token'];
            file_put_contents($tokenFile, $this->accessToken);
        }
    }

    
    private function logResult(string $title, string $author, string $url)
    {
        $logDate = [            
            $title,
            $author,
            $url,
            date('d-m-Y H:i:s'),
        ];

        $file = fopen('result.csv', 'a');
        fputcsv($file, $logDate);
        fclose($file);
    }

    public function createPage(string $title, array $content, string $authorName = 'AffnetixBot')
    {
        if($this->accessToken === null){
            throw new Exception('Спочатку необхідно ініціалізувати акаунт через initAccount()');
        }

        $url = $this->apiBaseUrl . 'createPage';
        $params = [
            'access_token' => $this->accessToken,
            'title' => $title,
            'author_name' => $authorName,
            'content' => json_encode($content),
            'return_content' => true
        ];

        $response = $this-> sendRequest($url, $params);

        if(!empty($response['ok']) && !empty($response['result']['url'])) {
            $this->logResult($title , 'AffnetixBot' , $response['result']['url']);
        }

        return $response;
    }
}