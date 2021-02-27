{{ header }}

<h2>Congratulation, you have a new order on {{ site_title }}!</h2>

<p>Hi, {{ customer_name }} has just ordered on your site</p>

<a href="{{ site_url }}/orders/tracking?order_id={{ order_id }}&email={{ customer_email }}" class="button button-blue">View order</a>

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

{{ footer }}
