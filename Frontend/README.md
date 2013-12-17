DEd C
=====
Checkout
--------
Use git to clone the repository:

    git clone https://github.com/IndustrialDragonfly/ded_c.git
    
Build Canvas
------------
    sudo apt-get install npm
    sudo npm install -g grunt-cli
    ln -sf /usr/bin/nodejs /usr/bin/node
    cd ded_c/Frontend/dev/canvas
    npm install
    grunt uglify
    grunt jsdoc #Build documentation (Optional)

Canvas will be built in dist/js/, and the documentation for Cavans will be built it dist/doc.
