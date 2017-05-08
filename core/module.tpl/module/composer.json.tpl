{
    "name": "[{$oModule->getVendorPrefix()}]/[{$oModule->getModuleId(false)}]",
    "description": "[{$oModule->getDescription()}]",
    "type": "oxideshop-module",
    "keywords": ["oxid", "modules", "eShop"],
    "homepage": "[{$oModule->getAuthorData('link')}]",
    "license": [
        "GPL-3.0",
        "proprietary"
    ],
    "extra": {
        "oxideshop": {
            "target-directory": "[{$oModule->getVendorPrefix()}]/[{$oModule->getModuleFolderName()}]"
        }
    }
}

