package elephantsgenerator;

import java.io.File;
import java.util.HashMap;
import java.util.List;
import javax.activation.MimetypesFileTypeMap;

public class ElephantsGenerator {
    File directory;
    ImageThumbnailGenerator imageThumbnailGenerator;
    String productsSql = "";
    String sizesSql = "";
    int productsTableId = 1;
    int sizesTableId = 1;

    public ElephantsGenerator(String directory,
                            int maxImageWidth,int maxImageHeight,
                            int maxThumbnailWidth,int maxThumbnailHeight,
                            String imagesDir,String thumbnailsDir) {
        this.directory = new File(directory);  
        this.imageThumbnailGenerator = new ImageThumbnailGenerator(maxImageWidth, maxImageHeight, maxThumbnailWidth, maxThumbnailHeight, imagesDir, thumbnailsDir);
    }
    
    public String init(File[] files){
        HashMap<String,Boolean>imgNamesList = new HashMap<>();
        HashMap<String,Boolean>namesList = new HashMap<>();
        productsTableId = 1;
        sizesTableId = 1;
        productsSql = "";
        sizesSql ="";
        setupData(files,namesList,imgNamesList);
        return productsSql + "\n" + sizesSql;
    }
    
    private void renameFile(File file,HashMap<String,Boolean>imgNamesList){
        String name = "_"+file.getName();
        String path = new File(file.getParent()).getAbsolutePath();
        while(imgNamesList.get(name)!=null){
            name = "_"+name;     
        }
        File file2 = new File(path+"\\"+name);
        file.renameTo(file2);
        imgNamesList.put(name, Boolean.TRUE);
    }
    
    private String getUniqueProductName(HashMap<String,Boolean>namesList){
        String name = DataGenerator.generateNameWithSize();
        while(namesList.get(name)!=null){
            name = DataGenerator.generateNameWithSize();     
        }
       return name;
    }
    
    private boolean fileIsImage(File file){
        String mimetype = new MimetypesFileTypeMap().getContentType(file);
        String type = mimetype.split("/")[0];
        return type.equals("image");
    }
    
    
    private void setupData(File[] files,HashMap<String,Boolean>namesList,HashMap<String,Boolean>imgNamesList){
        for (File file : files) {
            if (file.isDirectory()) {
                this.setupData(file.listFiles(),namesList,imgNamesList);
            } else {
                //skip non-image files
               if(!fileIsImage(file)) continue;
                
               //rename files without unique names
                if(imgNamesList.get(file.getName()) ==null)
                    imgNamesList.put(file.getName(), Boolean.TRUE);
                else 
                    renameFile(file, imgNamesList);
                
                //generate SQL data
                String name = getUniqueProductName(namesList);
                String category = new File(file.getParent()).getName();
                String img = file.getName();
                productsSql+=DataGenerator.getProductsTableSQL("product", productsTableId,name, img, category);
                List<String>sizes = DataGenerator.generateSizes();
                for(String size:sizes){
                    sizesSql+= DataGenerator.getSizesTableRowSQL("product_size", sizesTableId, productsTableId, size);
                    ++sizesTableId;
                }
                ++productsTableId;
                //create folder thumbnails and resized (by max width) images
                imageThumbnailGenerator.createThumbnailByWidth(file);
                imageThumbnailGenerator.createImageByWidth(file);

            }
        }
    }
 
    public static void main(String[] args) {
        // Generate thumnails, resized images and write sql request for generated data, output it
        ElephantsGenerator elephantsGen = new ElephantsGenerator("C:\\Elephants",1000,1000,400,400,"C:\\imgs","C:\\thumbs");
        String sql =  elephantsGen.init(elephantsGen.directory.listFiles());
        System.out.print(sql);

    }
    
}
