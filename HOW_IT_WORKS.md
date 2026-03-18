# How Your CLOTHR Website Works (A Simple Guide)

*Date: March 18, 2026*

This document explains where all your code lives and how the different pieces of your website talk to each other. Don't worry—you don't need to be a programmer to understand this! We will use simple examples to explain the architecture.

---

## 🌎 1. The Map (The "Routes" Folder)
Imagine your website is a big city. When a customer types a web address like `clothr.com/shop` in their browser, they are asking for directions.

The files inside the `routes/` folder are the **Map Directors**. They look at the address the customer typed and say, *"Ah, you want the Shop Page! Go talk to the Shop Traffic Cop."*

* **`routes/web.php`:** The map for all normal customers visiting your public store.
* **`routes/admin.php`:** The map for the hidden Admin Dashboard. This map has a lock on it—only logged-in admins can even look at it!
* **`routes/api.php`:** Special invisible routes used by your website's background JavaScript (like when the Cart updates behind the scenes without reloading the page).

---

## 👮‍♂️ 2. The Traffic Cops (The "Controllers" Folder)
Once the map directs the customer, they arrive at a **Controller**. Controllers live inside `app/Http/Controllers/`. This is where we did our massive Clean Architecture upgrade!

The Traffic Cop's job is to check three simple things:
1. **Who is arriving?** (Are they logged in as an admin? Are they a normal user?)
2. **Did they bring what they need?** (If they are buying a dress, did they give us a valid credit card and a shipping address?)
3. **Where should they go next?** (Tell the Workers to save the order, then finally show the customer the "Order Success!" page).

**Where to find them:**
* If you want to change how the Admin Dashboard pages load, look in `app/Http/Controllers/Admin/`.
* If you want to change how the Public Storefront pages load, look in `app/Http/Controllers/Shop/`.

---

## 🏗️ 3. The Heavy Lifters (The "Services" Folder)
Traffic Cops don't actually build the houses or calculate the math—they just direct traffic! For the actual hard work, the Traffic Cop calls a **Service Worker**.

Services live inside `app/Http/Services/`. They do the complicated math, talk to the database directly, save files, and double-check prices to stop hackers.

* **`OrderService.php`:** This worker's *only job* is taking an order, recalculating the total price directly from the database (so hackers can't change it to $0.00), reducing the stock inventory, and creating the receipt.
* **`ProductService.php`:** This worker's *only job* is saving a new product, uploading the pictures into the correct folder, and formatting the tricky sizes and colors data.
* **`ReportService.php`:** This worker handles all the crazy math required to build those beautiful charts on your Dashboard.

*Why is this good?* If you ever build a Mobile Phone App for CLOTHR in the future, the Mobile App can talk directly to these exact same Workers, meaning you only have to write the math rules *once*!

---

## 🗄️ 4. The Filing Cabinets (The "Models" Folder)
When the Workers (`Services`) need information like "What is the price of this Black Dress?", they open a filing cabinet to look at the database.

These filing cabinets are called **Models**, and they live in `app/Models/`.

* **`Product.php`:** Represents the table holding all your clothes, prices, and stock numbers.
* **`User.php`:** Represents the table holding your customers, their emails, their encrypted passwords, and whether they have the secret `is_admin` badge.
* **`Order.php`:** Represents the table tracking who bought what and when it shipped.

These files define the "rules" of the cabinet. For example, `Product.php` tells the system: *"Hey, the 'Sizes' row isn't just a text note—it's actually a list of tags (JSON), so keep it organized that way!"*

---

## 🎨 5. The Paint & Decorations (The "Views" Folder)
Finally, after the map directed the user, the Traffic Cop checked their ID, the Worker did the math, and the Filing Cabinet produced the data... the user needs to actually *see* the website!

The visual part of your website lives in `resources/views/`. These files use a language called "Blade" mixed with HTML and CSS.

* **`resources/views/layouts/`:** This holds the "frame" of your house. It contains the top Header, the CLOTHR logo, the fonts, and the bottom Footer. Every other page is injected into the middle of this frame!
* **`resources/views/shop/`:** Contains the specific designs for the storefront, like `cart.blade.php` and `checkout.blade.php`.
* **`resources/views/admin/`:** Contains the specific layouts for your dashboard, like `products.blade.php` and your big `orders.blade.php` tables.

If you ever want to change a button color, move a logo, or change the text on a web page—**you look in the `resources/views/` folder.**

---

## Putting It All Together (An Example)
Let's see the journey of adding a product to the cart:
1. Customer clicks "Add to Cart". The browser asks the **Map** (`routes/api.php`) where to go.
2. The Map sends the request to the `CartController` **Traffic Cop**. 
3. The Traffic Cop quickly yells down to the `CartItem` **Filing Cabinet** to save the product.
4. The cart icon on the front-end **View** immediately changes from `(0)` to `(1)`!
