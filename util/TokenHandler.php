<?php

require_once 'jwt/JWT.php';

class TokenHandler
{
    /**
     * getSignedJWTForLogin.
     *
     * @param mixed user
     *
     * @return string
     */
    public static function getSignedJWTForLogin($user)
    {
        $issued_at_time = new DateTimeImmutable('now', new DateTimeZone(Constants::JWT['TIMEZONE']));
        $token_expiration = $issued_at_time->modify(Constants::JWT['EXPIRATION'])->getTimestamp();
        $issuer = Constants::JWT['ISSUER'];
        $payload = [
            'data' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ],
            'iat' => $issued_at_time->getTimestamp(),
            'iss' => $issuer,
            'nbf' => $issued_at_time->getTimestamp(),
            'exp' => $token_expiration,
        ];

        return JWT::encode($payload, Constants::JWT['SECRET_KEY']);
    }

    public static function authenticate()
    {
        $headers = static::getAuthorizationHeader();
        if (!is_null($headers)) {
            $token = static::getBearerToken($headers);
            if (!is_null($token)) {
                return static::validateJWTFromUser($token);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getAuthorizationHeader()
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }

    public static function getBearerToken($headers)
    {
        // HEADER: Get the access token from the header
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * validateJWTFromUser.
     *
     * @param mixed encoded_token
     *
     * @return bool
     */
    public static function validateJWTFromUser($encoded_token)
    {
        try {
            $decoded_token = JWT::decode($encoded_token, Constants::JWT['SECRET_KEY'], [Constants::JWT['ALG']]);
            $is_token_iss_valid = (($decoded_token->iss === Constants::JWT['ISSUER']));
            $is_valid_token = $decoded_token && $is_token_iss_valid;

            return $is_valid_token;
        } catch (\Throwable $th) {
        }
    }

    /**
     * getJWTFormatedExpiration.
     *
     * @param mixed encoded_token
     *
     * @return string
     */
    public static function getJWTFormatedExpiration($encoded_token)
    {
        $decoded_token = JWT::decode($encoded_token, Constants::JWT['SECRET_KEY'], ['HS256']);
        $formated_expiration = ((new DateTimeImmutable('now', new DateTimeZone(Constants::JWT['TIMEZONE'])))->setTimestamp($decoded_token->exp))->format('d-m-Y H:i:s');

        return $formated_expiration;
    }

    /**
     * getJWTData.
     *
     * @return object
     */
    public static function getJWTData()
    {
        $data = null;
        $token = static::getBearerToken(static::getAuthorizationHeader());
        $decoded_token = JWT::decode($token, Constants::JWT['SECRET_KEY'], ['HS256']);
        $data = $decoded_token->data;

        return $data;
    }
}
