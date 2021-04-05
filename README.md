# Housing-Group-Functions

**Lambda Functions for the Faculty Housing Google Group.**

## How to Set Up

1. Clone this repo.
   ```bash
   $ git clone https://github.com/zhumingcheng697/Housing-Group-Functions.git
    ```

2. Install [`serverless`](https://serverless.com/) globally.
    ```bash
    $ npm install -g serverless
    ```

3. Install other necessary node modules.
    ```bash
    $ npm install
    ```

4. Configure `serverless` using [AWS access keys](https://bref.sh/docs/installation/aws-keys.html).
    ```bash
    $ serverless config credentials --provider aws --key <key> --secret <secret>
    ```

5. Install [Composer](https://getcomposer.org/) globally.

    - On a Linux / Unix / macOS machine, [install Composer locally](https://getcomposer.org/download/) and then move the downloaded `composer.phar` to `/usr/local/bin/composer`.
        ```bash
        $ mv composer.phar /usr/local/bin/composer
        ```

    - On a Windows machine, download and run [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) as detailed on the [Composer website](https://getcomposer.org/doc/00-intro.md#using-the-installer).

6. Install necessary PHP modules through Composer.
    ```bash
    $ composer install
    ```

7. Configure `email.json`.
    ```json
   {
     "host": <your_host>,
     "port": <your_port>,
     "encryption": <your_encryption_method>,
     "username": <your_username>,
     "password": <your_password>
   }
   ```
   > Property `encryption` accepts `"ssl"` and `"tls"`, and defaults to `null` if unset.

8. Deploy all lambdas using `serverless`.
    ```bash
    $ serverless deploy
    ```

## Serverless Commands

- Deploy an AWS application with all the lambdas:
    ```bash
    $ serverless deploy
    ```

- Invoke a lambda function:

    ```bash
    $ serverless invoke -f <function-name>
    
    $ serverless invoke -f <function-name> --data='{"key": "value", ...}'
    ```

- Delete the AWS application with all the lambdas:
    ```bash
    $ serverless remove
    ```
