    <?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Grant Types
    |--------------------------------------------------------------------------
    |
    | Your OAuth2 Server can issue an access token based on different grant
    | types you can even provide your own grant type.
    |
    | To choose which grant type suits your scenario, see
    | http://oauth2.thephpleague.com/authorization-server/which-grant
    |
    | Please see this link to find available grant types
    | http://git.io/vJLAv
    |
    */
    
    // Implement OAuth2 Authorization Server
    'grant_types' => [
        
        // Client Credentials Grant
        // Note: This grant is suitable and used for machine-to-machine authentication, 
        // for example for use in a cron job which is performing maintenance tasks over an API.
        'client_credentials' => [
            'class' => '\League\OAuth2\Server\Grant\ClientCredentialsGrant',
            'access_token_ttl' => 3600
        ],
        
        // Auth Code Grant
        'authorization_code' => [
            'class' => '\League\OAuth2\Server\Grant\AuthCodeGrant',
            'access_token_ttl' => 3600,
            'auth_token_ttl'   => 3600
        ],
        // Password Grant
        // Note: Ask the user for their username and password to authenticate every user come to the site
        // This grant is suitable for trusted clients such as a service’s own mobile client (mobile devices)
        'password' => [
            'class' => 'League\OAuth2\Server\Grant\PasswordGrant',
            'callback' => '\App\Http\Middleware\PasswordGrantVerifier@verify',
            'access_token_ttl' => 604800
        ],
        // Refresh Token Grant
        // Refresh token to retrieve a new access token with the same permissions as the old one
        'refresh_token' => [
            'class' => '\League\OAuth2\Server\Grant\RefreshTokenGrant',
            'access_token_ttl' => 3600,
            'refresh_token_ttl' => 36000
        ]        
    ],    

    /*
    |--------------------------------------------------------------------------
    | Output Token Type
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access
    | token and the resource server how to parse the access token used.
    |
    | Default value is League\OAuth2\Server\TokenType\Bearer
    |
    */

    'token_type' => 'League\OAuth2\Server\TokenType\Bearer',

    /*
    |--------------------------------------------------------------------------
    | State Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the state parameter is required in the query string.
    |
    */

    'state_param' => false,

    /*
    |--------------------------------------------------------------------------
    | Scope Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the scope parameter is required in the query string.
    |
    */

    'scope_param' => false,

    /*
    |--------------------------------------------------------------------------
    | Scope Delimiter
    |--------------------------------------------------------------------------
    |
    | Which character to use to split the scope parameter in the query string.
    |
    */

    'scope_delimiter' => ',',

    /*
    |--------------------------------------------------------------------------
    | Default Scope
    |--------------------------------------------------------------------------
    |
    | The default scope to use if not present in the query string.
    |
    */

    'default_scope' => null,

    /*
    |--------------------------------------------------------------------------
    | Access Token TTL
    |--------------------------------------------------------------------------
    |
    | For how long the issued access token is valid (in seconds) this can be
    | also set on a per grant-type basis.
    |
    */

    'access_token_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Limit clients to specific grants
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit clients to specific grant types. This is useful
    | to allow only trusted clients to access your API differently.
    |
    */

    'limit_clients_to_grants' => false,

    /*
    |--------------------------------------------------------------------------
    | Limit clients to specific scopes
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit clients to specific scopes. This is useful to
    | only allow specific clients to use some scopes.
    |
    */

    'limit_clients_to_scopes' => false,

    /*
    |--------------------------------------------------------------------------
    | Limit scopes to specific grants
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit scopes to specific grants. This is useful to
    | allow certain scopes to be used only with certain grant types.
    |
    */

    'limit_scopes_to_grants' => false,

    /*
    |--------------------------------------------------------------------------
    | HTTP Header Only
    |--------------------------------------------------------------------------
    |
    | This will tell the resource server where to check for the access_token.
    | By default it checks both the query string and the http headers.
    |
    */

    'http_headers_only' => false,

];
