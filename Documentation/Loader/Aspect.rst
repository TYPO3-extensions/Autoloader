Aspect
^^^^^^

The "Aspect" autoloader register registers aspects like before, replace, after and throw for all classes available. The aspect mechanism based on a xclass that extends the class to attach the aspects to the joinpoints. **Pitfall:** You can only use aspects on classes that doesn't been xclassed by any extension.