DEdC
====

DEdC is software to address the importance of collaboration during the architectural phase of a system's design, specifically the creation of dataflow diagrams (DFDs). A DFD allows its creator to track the flow of data across different components of their system, from the end user, to the server, and back again. DFDs, as such, are used not only when architecting a system, but for example to identify potential security threats, since it shows what data could be exposed to an attacker.

The approach taken by DEdC to improve on existing tools is to create a web-based, collaborative tool. DEdC uses a RESTful web architecture (an approach which treats resources on the server much like web servers treat individual pages) to allow for new clients to easily be implemented. DEdC itself provides a client which includes a partial subset of the server's current features to a user. The server includes a data model representing the complete DFD. This data model can be saved and restored to any storage mechanism (currently only MySQL is supported), which means that the data model objects could be implemented in other languages for more advanced analysis, such as applying machine learning technology in a language like Python.

DEdC will soon be released under the New BSD License, which will allow it to be freely integrated into most open- or closed-source products. DEdC has been designed with security in mind, and while still new enough it will likely have some security issues, it has a solid security architecture for use in corporate environments or new projects.
