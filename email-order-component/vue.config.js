const { defineConfig } = require('@vue/cli-service');

module.exports = defineConfig({
  transpileDependencies: true,
  configureWebpack: {
    output: {
      libraryExport: 'default',
      libraryTarget: 'umd',
      library: 'MyLibrary',
      filename: 'mylibrary.bundle.[chunkhash].js'
    },
    optimization: {
      splitChunks: false, 
    }
  }
});
