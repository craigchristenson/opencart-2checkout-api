### _[Signup free with 2Checkout and start selling!](https://www.2checkout.com/referral?r=git2co)_

### Integrate OpenCart with 2Checkout Payment API (Supports PayPal Direct)
----------------------------------------

### 2Checkout Payment API Setup

#### OpenCart Settings

1. Download the 2Checkout payment module from https://github.com/craigchristenson/opencart-2checkout-api
2. Upload the files to your OpenCart directory.
3. Sign in to your OpenCart admin.
4. Click **Extensions** tab and **Payments subtab**.
5. Under **2Checkout** click **Install** and then click **Edit**.
6. Enter your **2Checkout Account ID**. _(2Checkout Account Number)_
7. Enter your **Public Key**. _(2Checkout Publishable Key)_
8. Enter your **Private Key**. _(2Checkout Private Key)_
9. Select **No** under **Sandbox Mode**. _(Unless you are tesing in the 2Checkout Sandbox)_
10. Select **Complete** under **Order Status**.
11. Select **Enabled** under **Status**.
12. Save your changes.



### 2Checkout PayPal Direct Setup

#### OpenCart Settings

1. Sign in to your OpenCart admin.
2. Click **Extensions** tab and **Payments subtab**.
3. Under **2Checkout PayPal Direct** click **Install** and then click **Edit**.
4. Enter your **2Checkout Account ID**. _(2Checkout Account Number)_
5. Select **Complete** under **Order Status**.
6. Select **Enabled** under **Status**.
7. Enter your **Secret Word** _(Must be the same value entered on your 2Checkout Site Management page.)_
8. Save your changes.


#### 2Checkout Settings

1. Sign in to your 2Checkout account.
2. Click the **Account** tab and **Site Management** subcategory.
3. Under **Direct Return** select **Header Redirect** or **Given links back to my website**.
4. Enter your **Secret Word**._(Must be the same value entered in your OpenCart admin.)_
5. Set the **Approved URL** to http://www.yourstore.com/index.php?route=extension/payment/twocheckout\_pp/callback _(Replace http://www.yourstore.com with the actual URL to your store.)_
6. Click **Save Changes**.

Please feel free to contact 2Checkout directly with any integration questions.
