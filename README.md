
# odoo-integration

odoo-interation includes tiktok shop, woocommerce,...




## Requirements
- Service app in Tiktok Partner Center
- Test account shoper/buyer tikok
- An domain with ssl certificate to run webhook(You can use ngrok)
- Odoo
## Installation

Clone this project 

```bash
  git clone https://github.com/RealTun/odoo-integration
  cd odoo-integration
```

```bash
  composer install
```

```bash
  cp .env.example .env
  php artisan key:generate
```

- Let enter the necessary keys in env file
    
## Run Locally
Start the server

```bash
  php artisan serve
```

- Use ngrok to localhost to domain
```bash
  ngrok http --url=static_domain port
  + replace with the static_domain that ngrok has chosen for free
  + replace port you running server in previous step
```

- Go to service app in Partner Center change URL callback to:
    yourdomain/api/tiktok/auth/callback 
- Go to automations rules create trigger, choose model and trigger then add action is Send Webhook Notification, pick some fields you want, then fill url webhook:           
    yourdomain/api/odoo/webhook



## For more
You can see routes in api.php then let use and test :D