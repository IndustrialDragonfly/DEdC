DEd C
=====
Checkout
--------
Use git to clone the repository:

    git clone https://github.com/IndustrialDragonfly/ded_c.git
    
Build DEd C's Frontend
----------------------
    sudo apt-get install npm
    sudo npm install -g grunt-cli
    ln -sf /usr/bin/nodejs /usr/bin/node
    cd ded_c/
    ant build
    ant doc

DEd C's frontend will be built in Frontend/dist.
