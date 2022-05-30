
<style>
    p {
      font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;
    }

    #code {
        background-color: #EEE;
        font-size: 25px;
        padding: 15px;
        text-align: center;
        font-weight: bold;
        letter-spacing: 5px;
        font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;
    }
</style>

<p> {"Here is your verification code to connect to the Piwigo Gallery"|@translate} </p>

<div id="code">{$CODE}</div>

<p>{"The code is for one-time use only"|@translate}</p>

<p>{"Connection attempt information :"|@translate} {$DATE} {if $GEO_IP_INFO["valid"]} {$GEO_IP_INFO["country"]}, {$GEO_IP_INFO["region"]} {/if}</p>

<p>{"If you haven't logged in to Piwigo recently, we invite you to contact the webmaster and change your password."|@translate}</p>