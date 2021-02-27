{{ header }}

<h2>Order successfully!</h2>

<p>Hi {{ customer_name }},</p>
<p>Thank you for purchasing our products, we will contact you via phone <strong>{{ customer_phone }}</strong> to confirm order!</p>

<a href="{{ site_url }}/orders/tracking?order_id={{ order_id }}&email={{ customer_email }}" class="button button-blue">View order</a> or <a href="{{ site_url }}">Go to our shop</a>

<br />

<h3>Order information: </h3>

<p>Order number: <strong>#{{ order_id }}</strong></p>

{{ product_list }}

<h3>Customer information</h3>

<p>{{ customer_name }} - {{ customer_phone }}, {{ customer_address }}</p>

<h3>Shipping method</h3>
<p>{{ shipping_method }}</p>

<h3>Payment method</h3>
<p>{{ payment_method }}</p>

<br />

<p>If you have any question, please contact us via <a href="mailto:{{ site_admin_email }}">{{ site_admin_email }}</a></p>

{{ footer }}
